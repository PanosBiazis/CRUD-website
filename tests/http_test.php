<?php
declare(strict_types=1);
// Isolated real HTTP process, synthetic DB; no real MySQL or account data.
$root = dirname(__DIR__); $tmp = sys_get_temp_dir() . '/crud-test-' . bin2hex(random_bytes(8));
mkdir($tmp, 0700); $log = "$tmp/queries.jsonl"; touch($log); chmod($log, 0600);
$probe = stream_socket_server('tcp://127.0.0.1:0', $errno, $error);
$address = stream_socket_get_name($probe, false); fclose($probe);
$env = array_merge(getenv(), ['CRUD_DB_USER'=>'test','CRUD_DB_PASSWORD'=>'synthetic','CRUD_DB_NAME'=>'test', 'CRUD_WRITER_EMAILS'=>'writer@example.invalid','CRUD_TEST_LOG'=>$log]);
$process = proc_open([PHP_BINARY,'-n','-d','session.save_path='.$tmp,'-d','auto_prepend_file='.$root.'/tests/fixtures/recording_mysqli.php','-S',$address,'-t',$root.'/CRUD'], [0=>['file','/dev/null','r'],1=>['file',"$tmp/server.log",'a'],2=>['file',"$tmp/server.log",'a']], $pipes, $root, $env);
if (!is_resource($process)) { throw new RuntimeException('Server failed'); }
$count=0; $cookies=[];
function check(bool $ok, string $why): void { global $count; $count++; if (!$ok) { throw new RuntimeException($why); } }
function request(string $route, ?array $data=null): array {
    global $address,$cookies;
    $headers=''; foreach ($cookies as $name=>$value) { $headers .= "Cookie: $name=$value\r\n"; }
    $options=['method'=>$data===null?'GET':'POST','ignore_errors'=>true,'follow_location'=>0,'timeout'=>5,'header'=>$headers];
    if ($data!==null) { $options['header'].="Content-Type: application/x-www-form-urlencoded\r\n"; $options['content']=http_build_query($data); }
    $body=file_get_contents('http://'.$address.'/'.$route,false,stream_context_create(['http'=>$options]));
    $status=(int)explode(' ',$http_response_header[0])[1];
    foreach($http_response_header as $line) { if (preg_match('/^Set-Cookie: ([^=]+)=([^;]*)/i',$line,$m)) {$cookies[$m[1]]=$m[2];} }
    return [$status,$body,$http_response_header];
}
function token(string $body): string { if(!preg_match('/name="csrf" value="([a-f0-9]{64})"/',$body,$m)){throw new RuntimeException('Missing CSRF');} return $m[1]; }
try {
    $ready=false; for($i=0;$i<50;$i++){ $socket=@stream_socket_client('tcp://'.$address,$errno,$error,.1);if($socket){fclose($socket);$ready=true;break;}usleep(20000); }
    check($ready,'server ready');
    foreach(['Add.php','edit.php','update.php','delete.php','DeleteAll.php','select_delete.php','CRUD-Bike_Race.php'] as $route) { check(request($route)[0]===403,'anonymous denied '.$route); }
    check(filesize($log)===0,'unauthorized requests never touch DB');
    $cookies=[];
    [$status,$body,$headers]=request('Login.php');$csrf=token($body);
    check($status===200,'login form');check(str_contains(strtolower(implode('\n',$headers)),'httponly'),'HttpOnly cookie');
    check(request('Login.php',['email'=>'writer@example.invalid','password'=>'synthetic-test-password'])[0]===403,'missing CSRF');
    check(request('Register.php',['csrf'=>$csrf,'email'=>'writer@example.invalid','username'=>'Attacker','password'=>'synthetic-test-password','confirm_password'=>'synthetic-test-password'])[0]===400,'cannot self-register editor');
    [$status,$body,$headers]=request('Login.php',['csrf'=>$csrf,'email'=>'writer@example.invalid','password'=>'synthetic-test-password']);
    check($status===303,'editor login');check(in_array('Location: CRUD-Bike_Race.php',$headers,true),'fixed redirect');
    [$status,$body]=request('CRUD-Bike_Race.php');$csrf=token($body);check($status===200,'editor access');
    check(str_contains($body,'&lt;script&gt;alert(1)&lt;/script&gt;')&&!str_contains($body,'<script>'),'stored XSS escaped');
    check(request('delete.php')[0]===405,'GET delete blocked');
    clearstatcache();$before=filesize($log);check(request('DeleteAll.php')[0]===200,'delete-all GET is form');clearstatcache();check(filesize($log)===$before,'GET no DELETE');
    $record=['csrf'=>$csrf,'id'=>'7','name'=>"O'Reilly <script>",'country'=>'Example','time'=>'01:23'];
    foreach(['Add.php','update.php','delete.php'] as $route) { check(request($route,$record)[0]===303,'bound mutation '.$route); }
    check(request('update.php',array_replace($record,['id'=>'7 OR 1=1']))[0]===400,'ID injection rejected');
    check(request('DeleteAll.php',['csrf'=>$csrf,'confirmation'=>'no'])[0]===400,'delete all confirmation');
    check(request('DeleteAll.php',['csrf'=>$csrf,'confirmation'=>'DELETE ALL'])[0]===303,'delete all confirmed');
    check(request('CRUD-BR_User.php?sort=asc%20LIMIT%201000')[0]===400,'sort injection');
    check(request('CRUD-BR_User.php?page[]=1')[0]===400,'array page rejected');
    check(request('logout.php',['csrf'=>$csrf])[0]===303,'logout');check(request('Add.php')[0]===403,'logout revokes');
    [$s,$body]=request('Login.php');$csrf=token($body);
    check(request('Login.php',['csrf'=>$csrf,'email'=>'reader@example.invalid','password'=>'synthetic-test-password'])[0]===303,'reader login');
    check(request('Add.php')[0]===403,'reader cannot write');
    $queries=array_map(fn($line)=>json_decode($line,true),file($log,FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES));
    foreach($queries as $q){check(!str_contains($q['sql'],"O'Reilly")&&!str_contains($q['sql'],'<script>'),'values separate from SQL');}
    echo "HTTP tests: $count passed (synthetic DB)\n";
} finally {
    proc_terminate($process);proc_close($process);
    foreach(glob($tmp.'/*') as $file){unlink($file);}rmdir($tmp);
}
