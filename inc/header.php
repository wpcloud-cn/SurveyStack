<?php
$siteName = $siteName ?? 'SurveyStack';
?>
<!doctype html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?= e($title ?? $siteName) ?></title>
  <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<header class="topbar">
  <div class="container">
    <a href="/index.php" class="logo"><?= e($siteName) ?></a>
    <nav>
      <?php if (is_logged_in()): ?>
        <a href="/user/index.php">个人中心</a>
        <a href="/logout.php">退出</a>
      <?php else: ?>
        <a href="/login.php">登录</a>
        <a href="/register.php">注册</a>
      <?php endif; ?>
      <a href="/admin/index.php">后台</a>
    </nav>
  </div>
</header>
<main class="container">
