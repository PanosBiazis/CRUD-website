<?php
declare(strict_types=1);

final class CrudInputError extends RuntimeException {}
function crud_text(mixed $value, int $max = 240): string {
    if (!is_string($value) || trim($value) === '' || strlen($value) > $max
        || preg_match('//u', $value) !== 1 || preg_match('/[\x00-\x1f\x7f]/', $value)) {
        throw new CrudInputError('Invalid or missing text.');
    }
    return trim($value);
}
function crud_id(mixed $value, int $max = 2147483647): int {
    if (!is_string($value) || !preg_match('/^[1-9][0-9]{0,9}$/D', $value)
        || (float)$value > $max) { throw new CrudInputError('Invalid positive integer.'); }
    return (int)$value;
}
function crud_email(mixed $value): string {
    $email = crud_text($value, 254);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { throw new CrudInputError('Invalid email.'); }
    return strtolower($email);
}
function crud_password(mixed $value, bool $new = false): string {
    if (!is_string($value) || strlen($value) > 72 || strlen($value) < ($new ? 12 : 1)
        || str_contains($value, "\0")) { throw new CrudInputError('Invalid password length.'); }
    return $value;
}
function crud_h(mixed $value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
function crud_writer_allowed(array $session, string $configured): bool {
    $email = $session['email'] ?? null;
    if (!is_string($email) || $email === '' || !is_int($session['authenticated_at'] ?? null)
        || $session['authenticated_at'] > time() || time() - $session['authenticated_at'] >= 3600) { return false; }
    $writers = array_filter(array_map(fn($v) => strtolower(trim($v)), explode(',', $configured)));
    return in_array(strtolower($email), $writers, true);
}
function crud_csrf_valid(mixed $provided, mixed $expected): bool {
    return is_string($provided) && is_string($expected) && strlen($expected) === 64
        && strlen($provided) === 64 && hash_equals($expected, $provided);
}
// The fixed statements are shared by form and direct POST endpoints.
function crud_mutation(string $operation, array $input): array {
    if ($operation === 'delete_all') {
        if (($input['confirmation'] ?? null) !== 'DELETE ALL') { throw new CrudInputError('Confirmation required.'); }
        return ['DELETE FROM contestants', '', []];
    }
    $id = crud_id($input['id'] ?? null);
    $name = crud_text($input['name'] ?? null);
    if ($operation === 'delete') { return ['DELETE FROM contestants WHERE id = ? AND name = ?', 'is', [$id, $name]]; }
    $country = crud_text($input['country'] ?? null);
    $time = crud_text($input['time'] ?? null, 64);
    if ($operation === 'insert') { return ['INSERT INTO contestants (id, name, country, time) VALUES (?, ?, ?, ?)', 'isss', [$id, $name, $country, $time]]; }
    if ($operation === 'update') { return ['UPDATE contestants SET name = ?, country = ?, time = ? WHERE id = ?', 'sssi', [$name, $country, $time, $id]]; }
    throw new CrudInputError('Unsupported operation.');
}
