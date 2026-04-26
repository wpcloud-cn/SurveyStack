<?php
require __DIR__ . '/../inc/bootstrap.php';
require_admin();
$id=(int)($_GET['id']??0);
$st=$db->prepare('SELECT r.*,u.username,s.title FROM responses r LEFT JOIN users u ON u.id=r.user_id JOIN surveys s ON s.id=r.survey_id WHERE r.id=?');$st->execute([$id]);$r=$st->fetch();if(!$r)exit('不存在');
$a=$db->prepare('SELECT q.question_text,a.answer_text FROM answers a JOIN questions q ON q.id=a.question_id WHERE a.response_id=?');$a->execute([$id]);
include __DIR__ . '/../inc/header.php'; ?>
<h1>提交详情 #<?=$id?></h1><p>问卷：<?=e($r['title'])?>；用户：<?=e((string)$r['username'])?>；时间：<?=e($r['created_at'])?></p>
<?php foreach($a->fetchAll() as $row):?><div class="card"><strong><?=e($row['question_text'])?></strong><p><?=e((string)$row['answer_text'])?></p></div><?php endforeach;?>
<?php include __DIR__ . '/../inc/footer.php'; ?>
