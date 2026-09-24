<?php
declare(strict_types=1);

function envv(string $key, ?string $default = null): ?string {
    static $env = null;
    if ($env === null) {
        $env = [];
        $file = __DIR__ . '/.env';
        if (is_file($file)) {
            foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
                $line = trim($line);
                if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) continue;
                [$k, $v] = explode('=', $line, 2);
                $env[trim($k)] = trim($v);
            }
        }
    }
    return $env[$key] ?? $default;
}

function db(): PDO {
    static $pdo = null;
    if (!$pdo) {
        $dsn = 'mysql:host=' . envv('DB_HOST', 'localhost') .
               ';port=' . envv('DB_PORT', '3306') .
               ';dbname=' . envv('DB_NAME', 'travelsite') .
               ';charset=utf8mb4';
        $pdo = new PDO($dsn, envv('DB_USER', ''), envv('DB_PASS', ''), [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }
    return $pdo;
}

function e(mixed $value): string { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
function csrf(): string {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function check_csrf(): void {
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
        http_response_code(419);
        exit('Invalid CSRF token');
    }
}
function auth(): bool { return !empty($_SESSION['admin_id']); }
function require_auth(): void {
    if (!auth()) { header('Location: ?page=login'); exit; }
}
function audit(string $action, string $entity = '', ?int $entityId = null): void {
    try {
        $stmt = db()->prepare('INSERT INTO audit_logs(admin_user_id,action,entity,entity_id,ip_address) VALUES(?,?,?,?,?)');
        $stmt->execute([$_SESSION['admin_id'] ?? null, $action, $entity, $entityId, $_SERVER['REMOTE_ADDR'] ?? '']);
    } catch (Throwable) {}
}
