<?php
// Synthetic DB boundary for CLI HTTP tests only. Never load in deployment.
declare(strict_types=1);
if (extension_loaded('mysqli')) { throw new RuntimeException('Run the fixture with php -n'); }
define('MYSQLI_REPORT_ERROR', 1); define('MYSQLI_REPORT_STRICT', 2);
define('MYSQLI_OPT_CONNECT_TIMEOUT', 0); define('MYSQLI_ASSOC', 1);
function mysqli_report(int $flags): void {}
function mysqli_init(): mysqli { return new mysqli(); }
class mysqli {
    public function options(int $key, int $value): void {}
    public function real_connect(string ...$args): void {}
    public function set_charset(string $value): void {}
    public function prepare(string $sql): mysqli_stmt { return new mysqli_stmt($sql); }
}
class mysqli_stmt {
    private array $values = [];
    public function __construct(private string $sql) {}
    public function bind_param(string $types, mixed &...$values): void { $this->values = $values; }
    public function execute(): void {
        if (substr_count($this->sql, '?') !== count($this->values)) { throw new RuntimeException('Arity mismatch'); }
        file_put_contents(getenv('CRUD_TEST_LOG'), json_encode(['sql' => $this->sql, 'values' => $this->values]) . "\n", FILE_APPEND | LOCK_EX);
    }
    public function close(): void {}
    public function get_result(): object {
        $rows = [];
        if (str_starts_with($this->sql, 'SELECT email')) {
            $email = $this->values[0];
            if (in_array($email, ['writer@example.invalid','reader@example.invalid'], true)) {
                $rows = [['email' => $email, 'password' => password_hash('synthetic-test-password', PASSWORD_BCRYPT), 'username' => '<script>private</script>']];
            }
        } elseif (str_starts_with($this->sql, 'SELECT id')) {
            $rows = [['id'=>1,'name'=>'<script>alert(1)</script>','country'=>'A&B','time'=>'01:23']];
        }
        return new class($rows) {
            public function __construct(private array $rows) {}
            public function fetch_all(int $mode): array { return $this->rows; }
        };
    }
}
