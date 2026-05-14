<?php

declare(strict_types=1);

if (is_file(__DIR__ . '/local.php')) {
    require_once __DIR__ . '/local.php';
}

function db_config_value(string $constant, string $env, string $default): string
{
    if (defined($constant)) {
        return (string) constant($constant);
    }

    $value = getenv($env);

    return $value !== false && $value !== '' ? $value : $default;
}

function db(): ?PDO
{
    static $pdo = null;
    static $failed = false;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    if ($failed) {
        return null;
    }

    $host = db_config_value('DB_HOST', 'DB_HOST', '127.0.0.1');
    $database = db_config_value('DB_NAME', 'DB_NAME', 'aus37757_dbz');
    $user = db_config_value('DB_USER', 'DB_USER', 'root');
    $password = db_config_value('DB_PASS', 'DB_PASS', '');
    $charset = db_config_value('DB_CHARSET', 'DB_CHARSET', 'utf8mb4');

    $dsn = "mysql:host={$host};dbname={$database};charset={$charset}";

    try {
        $pdo = new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    } catch (PDOException $exception) {
        $_SESSION['db_error'] = $exception->getMessage();
        $failed = true;

        return null;
    }

    return $pdo;
}
