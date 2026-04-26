<?php

declare(strict_types=1);

if (file_exists(__DIR__ . '/config.php') && file_exists(__DIR__ . '/install.lock')) {
    exit('Already installed. Remove install.lock to reinstall.');
}

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $driver = $_POST['driver'] ?? 'sqlite';
    $baseUrl = rtrim($_POST['base_url'] ?? '', '/');
    $adminPath = trim($_POST['admin_path'] ?? 'admin', '/');
    $adminUser = trim($_POST['admin_user'] ?? 'admin');
    $adminPass = $_POST['admin_pass'] ?? 'admin123456';

    try {
        if ($driver === 'sqlite') {
            $sqlitePath = __DIR__ . '/data/database.sqlite';
            if (!is_dir(dirname($sqlitePath))) mkdir(dirname($sqlitePath), 0777, true);
            $pdo = new PDO('sqlite:' . $sqlitePath);
            $dbCfg = "'driver'=>'sqlite','path'=>'{$sqlitePath}'";
            $sqlFile = __DIR__ . '/sql/sqlite.sql';
        } else {
            $host = $_POST['db_host'] ?? '127.0.0.1';
            $port = $_POST['db_port'] ?? '3306';
            $name = $_POST['db_name'] ?? '';
            $user = $_POST['db_user'] ?? '';
            $pass = $_POST['db_pass'] ?? '';
            $pdo = new PDO("mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4", $user, $pass);
            $dbCfg = "'driver'=>'mysql','host'=>'{$host}','port'=>'{$port}','name'=>'{$name}','user'=>'{$user}','pass'=>'{$pass}'";
            $sqlFile = __DIR__ . '/sql/mysql.sql';
        }

        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec(file_get_contents($sqlFile));

        $hash = password_hash($adminPass, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO admins (username,password_hash,created_at) VALUES (?,?,?)');
        $stmt->execute([$adminUser, $hash, date('Y-m-d H:i:s')]);

        $defaults = [
            ['site_name', 'SurveyStack'], ['site_logo_text', 'SurveyStack'], ['footer_text', 'Powered by SurveyStack'],
            ['smtp_host', ''], ['smtp_port', '587'], ['smtp_user', ''], ['smtp_pass', ''], ['smtp_secure', 'tls'],
            ['mail_tpl_activation', '欢迎注册，点击链接激活：{{link}}'],
            ['mail_tpl_reset', '点击链接重置密码：{{link}}'],
        ];
        $st = $pdo->prepare('INSERT INTO settings (config_key,config_value) VALUES (?,?)');
        foreach ($defaults as $d) $st->execute($d);

        $config = "<?php\nreturn [\n  'app'=>['base_url'=>'{$baseUrl}','admin_path'=>'{$adminPath}'],\n  'db'=>[{$dbCfg}]\n];\n";
        file_put_contents(__DIR__ . '/config.php', $config);
        file_put_contents(__DIR__ . '/install.lock', 'locked');
        header('Location: /admin/index.php?installed=1');
        exit;
    } catch (Throwable $e) {
        $err = $e->getMessage();
    }
}
?>
<!doctype html><html lang="zh-CN"><head><meta charset="utf-8"><title>SurveyStack 安装</title><link rel="stylesheet" href="/assets/css/style.css"></head>
<body><main class="container"><h1>SurveyStack 安装</h1>
<?php if($err): ?><p class="error"><?= htmlspecialchars($err) ?></p><?php endif; ?>
<form method="post" class="card">
<label>站点基础 URL<input name="base_url" placeholder="/survey"></label>
<label>管理员目录名<input name="admin_path" value="admin"></label>
<label>数据库类型
<select name="driver" id="driver"><option value="sqlite">SQLite</option><option value="mysql">MySQL</option></select></label>
<div id="mysql-fields" style="display:none">
<label>MySQL 主机<input name="db_host" value="127.0.0.1"></label>
<label>端口<input name="db_port" value="3306"></label>
<label>数据库名<input name="db_name"></label>
<label>用户名<input name="db_user"></label>
<label>密码<input type="password" name="db_pass"></label>
</div>
<label>管理员用户名<input name="admin_user" value="admin"></label>
<label>管理员密码<input type="password" name="admin_pass" value="admin123456"></label>
<button type="submit">安装</button>
</form></main>
<script>const d=document.getElementById('driver'),m=document.getElementById('mysql-fields');d.onchange=()=>m.style.display=d.value==='mysql'?'block':'none';</script></body></html>
