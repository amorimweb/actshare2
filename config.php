<?php
// Carrega .env se existir
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) continue;
        [$key, $value] = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value);
    }
}

function env(string $key, string $default = ''): string {
    return $_ENV[$key] ?? getenv($key) ?: $default;
}

define('DB_HOST',     env('DB_HOST', 'localhost'));
define('DB_PORT',     env('DB_PORT', '3306'));
define('DB_USER',     env('DB_USER', 'root'));
define('DB_PASS',     env('DB_PASSWORD', ''));
define('DB_NAME',     env('DB_NAME', 'actshare'));
define('JWT_SECRET',  env('JWT_SECRET', 'change-me-in-production'));
define('SITE_URL',    env('SITE_URL', 'http://localhost'));
define('COOKIE_NAME', 'act-token');
define('JWT_TTL',     8 * 3600); // 8 horas
define('BASE_PATH',   rtrim(parse_url(SITE_URL, PHP_URL_PATH) ?? '', '/'));
