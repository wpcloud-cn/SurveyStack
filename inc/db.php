<?php

declare(strict_types=1);

function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $configPath = __DIR__ . '/../config.php';
    if (!file_exists($configPath)) {
        throw new RuntimeException('Missing config.php');
    }

    $config = require $configPath;
    $db = $config['db'];

    if ($db['driver'] === 'sqlite') {
        $pdo = new PDO('sqlite:' . $db['path']);
    } else {
        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $db['host'], $db['port'], $db['name']);
        $pdo = new PDO($dsn, $db['user'], $db['pass']);
    }

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
}

function run_sql_file(PDO $pdo, string $file): void
{
    $sql = file_get_contents($file);
    if ($sql === false) {
        throw new RuntimeException('Cannot read SQL file');
    }
    $pdo->exec($sql);
}
