<?php

declare(strict_types=1);

function send_mail(string $to, string $subject, string $html): bool
{
    $stmt = db()->query("SELECT config_key, config_value FROM settings WHERE config_key LIKE 'smtp_%' OR config_key LIKE 'mail_tpl_%'");
    $settings = [];
    foreach ($stmt->fetchAll() as $row) {
        $settings[$row['config_key']] = $row['config_value'];
    }

    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8\r\n";
    $from = $settings['smtp_user'] ?? 'no-reply@example.com';
    $headers .= 'From: ' . $from . "\r\n";

    return mail($to, $subject, $html, $headers);
}
