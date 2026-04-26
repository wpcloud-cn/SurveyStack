<?php

declare(strict_types=1);

$root = dirname(__DIR__);
if (!file_exists($root . '/config.php')) {
    if (basename($_SERVER['SCRIPT_NAME']) !== 'install.php') {
        header('Location: /install.php');
        exit;
    }
}

require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/mailer.php';

$config = file_exists($root . '/config.php') ? require $root . '/config.php' : [];
$db = db();
