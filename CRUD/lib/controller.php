<?php
declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';
function crud_page(string $page): void {
    $post = ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
    $writePages = ['add', 'edit', 'update', 'delete', 'select_delete', 'delete_all', 'manage'];
    if (in_array($page, $writePages, true)) { crud_require_writer(); }
    if ($page === 'logout') {
        crud_require_post(); $_SESSION = []; session_destroy();
        setcookie(session_name(), '', ['expires' => time() - 3600, 'path' => '/', 'secure' => ($_SERVER['HTTPS'] ?? '') === 'on', 'httponly' => true, 'samesite' => 'Strict']);
        crud_redirect('Start.php');
    }
    if ($page === 'update' || $page === 'delete' || ($post && in_array($page, ['add', 'edit', 'delete_all'], true))) {
        crud_require_post();
        $operation = ['add' => 'insert', 'edit' => 'update', 'update' => 'update', 'delete' => 'delete', 'delete_all' => 'delete_all'][$page];
        [$sql, $types, $values] = crud_mutation($operation, $_POST);
        $statement = crud_query($sql, $types, $values); $statement->close();
        crud_redirect('CRUD-Bike_Race.php');
    }
    if ($page === 'login' || $page === 'register') {
        $message = '';
        if ($post) {
            crud_require_post(); $email = crud_email($_POST['email'] ?? null);
            $password = crud_password($_POST['password'] ?? null, $page === 'register');
            if ($page === 'register') {
                // Public registration cannot claim an operator-reserved identity.
                if (crud_writer_allowed(['email' => $email, 'authenticated_at' => time()], getenv('CRUD_WRITER_EMAILS') ?: '')) {
                    throw new CrudInputError('Reserved account');
                }
                if ($password !== ($_POST['confirm_password'] ?? null)) { throw new CrudInputError('Passwords differ'); }
                $name = crud_text($_POST['username'] ?? null, 80); $hash = password_hash($password, PASSWORD_DEFAULT);
                $statement = crud_query('INSERT INTO accounts (email, password, username) VALUES (?, ?, ?)', 'sss', [$email, $hash, $name]); $statement->close();
                crud_redirect('Login.php');
            }
            $users = crud_rows('SELECT email, password, username FROM accounts WHERE email = ? LIMIT 2', 's', [$email]);
            // Fixed dummy hash avoids skipping the expensive verifier for unknown accounts.
            $hash = count($users) === 1 ? $users[0]['password'] : '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.';
            $valid = password_verify($password, $hash);
            if ($valid && count($users) === 1) {
                if (!session_regenerate_id(true)) { throw new RuntimeException('Session unavailable'); }
                $_SESSION = ['email' => $email, 'authenticated_at' => time(), 'csrf' => bin2hex(random_bytes(32))];
                crud_redirect(crud_writer_allowed($_SESSION, getenv('CRUD_WRITER_EMAILS') ?: '') ? 'CRUD-Bike_Race.php' : 'CRUD-BR_User.php');
            }
            $message = 'Invalid email or password.';
        }
        crud_header($page === 'login' ? 'Sign in' : 'Create account');
        if ($message !== '') { echo '<p role="alert">' . crud_h($message) . '</p>'; }
        echo '<form method="post" class="card">'; crud_csrf_field();
        if ($page === 'register') { crud_input('username', 'Display name', 'text', 80); }
        crud_input('email', 'Email', 'email', 254); crud_input('password', $page === 'register' ? 'Password (12–72 bytes)' : 'Password', 'password', 72);
        if ($page === 'register') { crud_input('confirm_password', 'Confirm password', 'password', 72); }
        echo '<button>Continue</button></form>'; crud_footer(); return;
    }
    if (in_array($page, ['add', 'edit', 'select_delete', 'delete_all'], true)) {
        $titles = ['add' => 'Add contestant', 'edit' => 'Edit contestant', 'select_delete' => 'Delete contestant', 'delete_all' => 'Delete all contestants'];
        crud_header($titles[$page]);
        echo '<form method="post" action="' . ($page === 'select_delete' ? 'delete.php' : '') . '" class="card">'; crud_csrf_field();
        if ($page === 'delete_all') { echo '<p>This removes every contestant. It cannot be undone.</p>'; crud_input('confirmation', 'Type DELETE ALL', 'text', 10); }
        else { crud_input('id', 'ID', 'text', 10); crud_input('name', 'Name');
            if ($page !== 'select_delete') { crud_input('country', 'Country'); crud_input('time', 'Time', 'text', 64); }
        }
        echo '<button' . (in_array($page, ['select_delete', 'delete_all'], true) ? ' class="danger"' : '') . '>Confirm</button></form>'; crud_footer(); return;
    }
    if (in_array($page, ['results', 'manage', 'search'], true)) {
        $number = isset($_GET['page']) ? crud_id($_GET['page'], 1000000) : 1;
        $sort = $_GET['sort'] ?? 'asc'; if (!is_string($sort) || !in_array($sort, ['asc', 'desc'], true)) { throw new CrudInputError('Invalid sort'); }
        $offset = ($number - 1) * 25; $direction = $sort === 'desc' ? 'DESC' : 'ASC';
        $query = $_GET['search_query'] ?? ''; if (!is_string($query)) { throw new CrudInputError('Invalid search'); }
        if ($query !== '') { $query = crud_text($query); $rows = crud_rows('SELECT id, name, country, time FROM contestants WHERE CAST(id AS CHAR) = ? OR name = ? OR country = ? OR time = ? ORDER BY id ' . $direction . ' LIMIT 26 OFFSET ?', 'ssssi', [$query, $query, $query, $query, $offset]); }
        else { $rows = crud_rows('SELECT id, name, country, time FROM contestants ORDER BY id ' . $direction . ' LIMIT 26 OFFSET ?', 'i', [$offset]); }
        $more = count($rows) > 25; $rows = array_slice($rows, 0, 25); crud_header($page === 'manage' ? 'Manage results' : 'Race results');
        if ($page === 'manage') { echo '<nav class="actions"><a href="Add.php">Add</a><a href="edit.php">Edit</a><a href="select_delete.php">Delete one</a><a href="DeleteAll.php">Delete all…</a></nav>'; }
        echo '<form method="get" class="search"><label>Search<input name="search_query" maxlength="240" value="' . crud_h($query) . '"></label><label>Order<select name="sort"><option value="asc">ID ascending</option><option value="desc"' . ($sort === 'desc' ? ' selected' : '') . '>ID descending</option></select></label><button>Search</button></form>';
        crud_table($rows); echo '<nav aria-label="Pagination">';
        foreach (['Previous' => $number - 1, 'Next' => $number + 1] as $label => $value) {
            if (($label === 'Previous' && $number > 1) || ($label === 'Next' && $more && $number < 1000000)) { echo '<a href="?' . crud_h(http_build_query(['page' => $value, 'sort' => $sort, 'search_query' => $query])) . '">' . $label . '</a>'; }
        }
        echo '</nav>'; crud_footer(); return;
    }
    crud_header('Bike Race'); echo '<section class="card"><p>Browse race results or sign in to manage the shared table.</p><p>Only accounts explicitly authorized by the local operator can change records.</p><a class="button" href="CRUD-BR_User.php">View results</a></section>'; crud_footer();
}
