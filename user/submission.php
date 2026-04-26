<?php
require __DIR__ . '/../inc/bootstrap.php';
require_login();
$id=(int)($_GET['id']??0);
$r=$db->prepare('SELECT * FROM responses WHERE id=? AND user_id=?');$r->execute([$id,$_SESSION['user_id']]);$resp=$r->fetch();if(!$resp) exit('不存在');
$a=$db->prepare('SELECT q.question_text,a.answer_text FROM answers a JOIN questions q ON q.id=a.question_id WHERE a.response_id=?');$a->execute([$id]);
include __DIR__ . '/../inc/header.php'; ?>
<h1>提交详情</h1>
<?php foreach($a->fetchAll() as $row):?><div class="card"><strong><?=e($row['question_text'])?></strong><p><?=e((string)$row['answer_text'])?></p></div><?php endforeach;?>
<?php include __DIR__ . '/../inc/footer.php'; ?>
