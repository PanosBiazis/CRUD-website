<?php
declare(strict_types=1);
require_once __DIR__ . '/security.php';
header('Content-Type: text/html; charset=UTF-8');
header('Cache-Control: no-store');
header('Referrer-Policy: no-referrer');
header('X-Content-Type-Options: nosniff');
header("Content-Security-Policy: default-src 'self'; script-src 'none'; style-src 'self'; img-src 'self'; base-uri 'none'; frame-ancestors 'none'; form-action 'self'");
ini_set('display_errors', '0');
set_exception_handler(function (Throwable $error): void {
    $input = $error instanceof CrudInputError;
    http_response_code($input ? 400 : 503);
    echo $input ? 'Invalid request. Check the fields and try again.' : 'The service is unavailable. Please try again later.';
});
ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');
session_name('crud_session');
session_set_cookie_params(['lifetime' => 0, 'path' => '/', 'secure' => ($_SERVER['HTTPS'] ?? '') === 'on', 'httponly' => true, 'samesite' => 'Strict']);
if (!session_start()) { throw new RuntimeException('Session unavailable'); }
if (!is_string($_SESSION['csrf'] ?? null) || strlen($_SESSION['csrf']) !== 64) { $_SESSION['csrf'] = bin2hex(random_bytes(32)); }
function crud_require_post(): void {
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
        header('Allow: POST'); http_response_code(405); exit('POST required.');
    }
    if (!crud_csrf_valid($_POST['csrf'] ?? null, $_SESSION['csrf'] ?? null)) { http_response_code(403); exit('Request verification failed.'); }
}
function crud_require_writer(): void {
    if (!crud_writer_allowed($_SESSION, getenv('CRUD_WRITER_EMAILS') ?: '')) { http_response_code(403); exit('Sign in with an authorized editor account.'); }
}
function crud_csrf_field(): void { echo '<input type="hidden" name="csrf" value="' . crud_h($_SESSION['csrf']) . '">'; }
function crud_redirect(string $route): never { header('Location: ' . $route, true, 303); exit; }
function crud_db(): mysqli {
    static $db = null;
    if ($db !== null) { return $db; }
    $user = getenv('CRUD_DB_USER'); $password = getenv('CRUD_DB_PASSWORD'); $name = getenv('CRUD_DB_NAME');
    if ($user === false || $user === '' || $password === false || $password === '' || $name === false || $name === '') {
        throw new RuntimeException('Database configuration unavailable');
    }
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $db = mysqli_init(); $db->options(MYSQLI_OPT_CONNECT_TIMEOUT, 5);
    $db->real_connect(getenv('CRUD_DB_HOST') ?: '127.0.0.1', $user, $password, $name);
    $db->set_charset('utf8mb4'); return $db;
}
function crud_query(string $sql, string $types = '', array $values = []): mysqli_stmt {
    $statement = crud_db()->prepare($sql);
    try {
        if ($types !== '') { $statement->bind_param($types, ...$values); }
        $statement->execute(); return $statement;
    } catch (Throwable $error) { $statement->close(); throw $error; }
}
function crud_rows(string $sql, string $types = '', array $values = []): array {
    $statement = crud_query($sql, $types, $values);
    try { return $statement->get_result()->fetch_all(MYSQLI_ASSOC); }
    finally { $statement->close(); }
}
function crud_header(string $title): void {
    echo '<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="stylesheet" href="assets/site.css"><link rel="icon" href="assets/icon.svg"><title>' . crud_h($title) . ' · Bike Race</title></head><body><header><a class="brand" href="Start.php"><img src="assets/icon.svg" alt="" width="36" height="36">Bike Race</a><nav><a href="CRUD-BR_User.php">Results</a><a href="Login.php">Sign in</a><a href="Register.php">Register</a>';
    if (crud_writer_allowed($_SESSION, getenv('CRUD_WRITER_EMAILS') ?: '')) { echo '<a href="CRUD-Bike_Race.php">Manage</a>'; }
    if (isset($_SESSION['email'])) { echo '<form action="logout.php" method="post">'; crud_csrf_field(); echo '<button>Sign out</button></form>'; }
    echo '</nav></header><main><h1>' . crud_h($title) . '</h1>';
}
function crud_footer(): void { echo '</main><footer>Local learning project · Do not use real personal data in this preview.</footer></body></html>'; }
function crud_table(array $rows): void {
    if (!$rows) { echo '<p>No records found.</p>'; return; }
    echo '<div class="table-scroll"><table><thead><tr><th>ID</th><th>Name</th><th>Country</th><th>Time</th></tr></thead><tbody>';
    foreach ($rows as $row) { echo '<tr>'; foreach (['id', 'name', 'country', 'time'] as $key) { echo '<td>' . crud_h($row[$key] ?? '') . '</td>'; } echo '</tr>'; }
    echo '</tbody></table></div>';
}
function crud_input(string $name, string $label, string $type = 'text', int $max = 240): void {
    echo '<label>' . crud_h($label) . '<input name="' . crud_h($name) . '" type="' . crud_h($type) . '" maxlength="' . $max . '" required></label>';
}
