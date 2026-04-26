<?php
require __DIR__ . '/../inc/bootstrap.php';
require_admin();
$surveyId=(int)($_GET['survey_id']??0);
if(isset($_GET['export'])&&$surveyId){
  header('Content-Type:text/csv');header('Content-Disposition:attachment; filename="survey_'.$surveyId.'.csv"');
  $out=fopen('php://output','w');fputcsv($out,['response_id','question','answer','time']);
  $st=$db->prepare('SELECT a.response_id,q.question_text,a.answer_text,r.created_at FROM answers a JOIN questions q ON q.id=a.question_id JOIN responses r ON r.id=a.response_id WHERE r.survey_id=? ORDER BY a.response_id');$st->execute([$surveyId]);
  foreach($st->fetchAll() as $row){fputcsv($out,$row);}fclose($out);exit;
}
$surveys=$db->query('SELECT id,title FROM surveys ORDER BY id DESC')->fetchAll();
$records=[];$stats=[];
if($surveyId){
 $st=$db->prepare('SELECT * FROM responses WHERE survey_id=? ORDER BY id DESC');$st->execute([$surveyId]);$records=$st->fetchAll();
 $q=$db->prepare('SELECT id,question_text,type FROM questions WHERE survey_id=?');$q->execute([$surveyId]);
 foreach($q->fetchAll() as $qu){
   $a=$db->prepare('SELECT answer_text,COUNT(*) c FROM answers a JOIN responses r ON r.id=a.response_id WHERE r.survey_id=? AND a.question_id=? GROUP BY answer_text');$a->execute([$surveyId,$qu['id']]);
   $stats[$qu['question_text']]=$a->fetchAll();
 }
}
include __DIR__ . '/../inc/header.php'; ?>
<h1>结果查看</h1>
<form><select name="survey_id"><?php foreach($surveys as $s):?><option value="<?=$s['id']?>" <?=$surveyId===$s['id']?'selected':''?>><?=e($s['title'])?></option><?php endforeach;?></select><button>查看</button><?php if($surveyId):?><a class="btn" href="?survey_id=<?=$surveyId?>&export=1">导出CSV</a><?php endif;?></form>
<?php foreach($records as $r):?><div class="card"><a href="/admin/view_response.php?id=<?=$r['id']?>">提交 #<?=$r['id']?></a> <?=e($r['created_at'])?></div><?php endforeach;?>
<h2>简单统计</h2>
<?php foreach($stats as $q=>$list):?><div class="card"><strong><?=e($q)?></strong><table><tr><th>选项</th><th>数量</th></tr><?php foreach($list as $i):?><tr><td><?=e((string)$i['answer_text'])?></td><td><?=$i['c']?></td></tr><?php endforeach;?></table></div><?php endforeach;?>
<?php include __DIR__ . '/../inc/footer.php'; ?>
