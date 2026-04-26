<?php
declare(strict_types=1);

date_default_timezone_set('Asia/Manila');

define('AUTH_DB_HOST', 'localhost');
define('AUTH_DB_NAME', 'carbon_tracker');
define('AUTH_DB_USER', 'root');
define('AUTH_DB_PASS', '');

define('TRACKER_DB_HOST', 'localhost');
define('TRACKER_DB_NAME', 'carbon_tracker');
define('TRACKER_DB_USER', 'root');
define('TRACKER_DB_PASS', '');

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
