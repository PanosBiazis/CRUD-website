<?php
declare(strict_types=1);
require __DIR__ . '/../CRUD/lib/security.php';
$count = 0;
function check(bool $ok, string $message): void { global $count; $count++; if (!$ok) { throw new RuntimeException($message); } }
function rejects(callable $fn): bool { try { $fn(); return false; } catch (CrudInputError) { return true; } }
foreach ([null, [], 1, '', '0', '-1', '1 OR 1=1', '1e3', '1.0', ' 1', '2147483648', str_repeat('9', 1000)] as $value) { check(rejects(fn() => crud_id($value)), 'invalid ID rejected'); }
check(crud_id('2147483647') === 2147483647, 'integer upper boundary');
foreach ([null, [], '', "bad\0text", str_repeat('x', 241), "\xff"] as $value) { check(rejects(fn() => crud_text($value)), 'invalid text rejected'); }
check(crud_text(' Ελληνικά ') === 'Ελληνικά', 'Unicode preserved');
$payload = ['id' => '7', 'name' => "O'Reilly <img src=x onerror=alert(1)>", 'country' => 'X & Y', 'time' => '1:23'];
foreach (['insert', 'update', 'delete'] as $op) {
    [$sql, $types, $values] = crud_mutation($op, $payload);
    check(!str_contains($sql, $payload['name']) && in_array($payload['name'], $values, true), 'payload stays outside SQL');
    check(substr_count($sql, '?') === strlen($types) && count($values) === strlen($types), 'parameter arity');
}
check(rejects(fn() => crud_mutation('update', array_replace($payload, ['id' => '7 OR 1=1']))), 'numeric injection refused');
check(rejects(fn() => crud_mutation('delete_all', [])), 'delete-all requires confirmation');
check(crud_mutation('delete_all', ['confirmation' => 'DELETE ALL'])[0] === 'DELETE FROM contestants', 'fixed delete-all statement');
check(crud_h('<script>"&') === '&lt;script&gt;&quot;&amp;', 'HTML escaping');
$token = str_repeat('a', 64);
check(crud_csrf_valid($token, $token), 'CSRF matches');
foreach ([null, [], '', str_repeat('b',64), str_repeat('a',65)] as $candidate) { check(!crud_csrf_valid($candidate, $token), 'bad CSRF refused'); }
$session = ['email' => 'writer@example.invalid', 'authenticated_at' => time()];
check(!crud_writer_allowed($session, ''), 'no editor by default');
check(crud_writer_allowed($session, 'writer@example.invalid'), 'configured editor');
check(!crud_writer_allowed($session, 'other@example.invalid'), 'other editor denied');
check(!crud_writer_allowed(['email' => $session['email'], 'authenticated_at' => time()-3600], $session['email']), 'expired editor denied');
check(!crud_writer_allowed(['email' => $session['email'], 'authenticated_at' => time()+1], $session['email']), 'future session denied');
check(rejects(fn() => crud_password(str_repeat('x',73), true)), 'no bcrypt truncation');
check(rejects(fn() => crud_password('short', true)), 'new short password rejected');
echo "security tests: $count passed\n";
