<?php
require __DIR__ . '/inc/bootstrap.php';
$siteName = 'SurveyStack';
$title = '问卷列表';
$now = date('Y-m-d H:i:s');
$stmt = $db->prepare("SELECT * FROM surveys WHERE is_public=1 AND (start_at IS NULL OR start_at<=?) AND (end_at IS NULL OR end_at>=?) ORDER BY id DESC");
$stmt->execute([$now, $now]);
$surveys = $stmt->fetchAll();
include __DIR__ . '/inc/header.php';
?>
<h1>公开问卷</h1>
<?php foreach ($surveys as $s): ?>
  <div class="card">
    <h3><?= e($s['title']) ?></h3>
    <p><?= nl2br(e((string)$s['description'])) ?></p>
    <a class="btn" href="/survey.php?id=<?= (int)$s['id'] ?>">开始填写</a>
  </div>
<?php endforeach; ?>
<?php include __DIR__ . '/inc/footer.php'; ?>
