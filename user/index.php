<?php
require __DIR__ . '/../inc/bootstrap.php';
require_login();
$user=current_user();
$stmt=$db->prepare('SELECT r.*,s.title FROM responses r JOIN surveys s ON s.id=r.survey_id WHERE r.user_id=? ORDER BY r.id DESC');
$stmt->execute([$_SESSION['user_id']]);$rows=$stmt->fetchAll();
include __DIR__ . '/../inc/header.php';
?>
<h1>个人中心</h1>
<p>欢迎，<?=e($user['username'])?></p>
<a class="btn" href="/user/profile.php">修改资料</a>
<h2>已填写问卷</h2>
<?php foreach($rows as $r):?><div class="card"><a href="/user/submission.php?id=<?=$r['id']?>"><?=e($r['title'])?></a> - <?=e($r['created_at'])?></div><?php endforeach;?>
<?php include __DIR__ . '/../inc/footer.php'; ?>
