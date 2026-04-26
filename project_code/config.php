<?php
declare(strict_types=1);

date_default_timezone_set('Asia/Manila');

function load_env_file(string $path): void
{
    if (!is_readable($path)) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    if ($lines === false) {
        return;
    }

    foreach ($lines as $line) {
        $entry = trim($line);

        if ($entry === '' || str_starts_with($entry, '#') || !str_contains($entry, '=')) {
            continue;
        }

        [$key, $value] = explode('=', $entry, 2);
        $key = trim($key);

        if ($key === '' || getenv($key) !== false) {
            continue;
        }

        $value = trim($value);

        if (str_starts_with($value, '"') && str_ends_with($value, '"')) {
            $value = substr($value, 1, -1);
        } elseif (str_starts_with($value, "'") && str_ends_with($value, "'")) {
            $value = substr($value, 1, -1);
        }

        putenv($key . '=' . $value);
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }
}

function env_value(string $key, string $default = ''): string
{
    $value = getenv($key);

    if ($value === false || $value === '') {
        return $default;
    }

    return $value;
}

load_env_file(__DIR__ . '/../.env.local');

define('AUTH_DB_HOST', env_value('AUTH_DB_HOST', 'localhost'));
define('AUTH_DB_NAME', env_value('AUTH_DB_NAME', 'carbon_tracker'));
define('AUTH_DB_USER', env_value('AUTH_DB_USER', 'root'));
define('AUTH_DB_PASS', env_value('AUTH_DB_PASS', ''));

define('TRACKER_DB_HOST', env_value('TRACKER_DB_HOST', AUTH_DB_HOST));
define('TRACKER_DB_NAME', env_value('TRACKER_DB_NAME', AUTH_DB_NAME));
define('TRACKER_DB_USER', env_value('TRACKER_DB_USER', AUTH_DB_USER));
define('TRACKER_DB_PASS', env_value('TRACKER_DB_PASS', AUTH_DB_PASS));

function create_pdo_connection(string $host, string $dbName, string $dbUser, string $dbPass): PDO
{
    $dsn = 'mysql:host=' . $host . ';dbname=' . $dbName . ';charset=utf8mb4';

    return new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
}

function auth_db(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $pdo = create_pdo_connection(
            AUTH_DB_HOST,
            AUTH_DB_NAME,
            AUTH_DB_USER,
            AUTH_DB_PASS
        );
    }

    return $pdo;
}

function tracker_db(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $pdo = create_pdo_connection(
            TRACKER_DB_HOST,
            TRACKER_DB_NAME,
            TRACKER_DB_USER,
            TRACKER_DB_PASS
        );
    }

    return $pdo;
}

function db(): PDO
{
    return tracker_db();
}
