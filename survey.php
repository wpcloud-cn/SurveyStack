<?php
require __DIR__ . '/inc/bootstrap.php';
$id=(int)($_GET['id']??0);
$page=max(1,(int)($_GET['page']??1));
$stmt=$db->prepare('SELECT * FROM surveys WHERE id=? AND is_public=1');$stmt->execute([$id]);$survey=$stmt->fetch();
if(!$survey) exit('问卷不存在');
if($survey['require_login'] && !is_logged_in()) redirect('/login.php');
$qs=$db->prepare('SELECT * FROM questions WHERE survey_id=? ORDER BY sort_order,id');$qs->execute([$id]);$questions=$qs->fetchAll();
$pageSize=max(1,(int)$survey['page_size']);$total=max(1,(int)ceil(count($questions)/$pageSize));$slice=array_slice($questions,($page-1)*$pageSize,$pageSize);

if($_SERVER['REQUEST_METHOD']==='POST'){
  verify_csrf();
  $ip=$_SERVER['REMOTE_ADDR']??'0.0.0.0';
  if($survey['limit_mode']==='ip'){$c=$db->prepare('SELECT COUNT(*) c FROM responses WHERE survey_id=? AND ip=?');$c->execute([$id,$ip]);if((int)$c->fetch()['c']>0)exit('当前IP已提交');}
  if($survey['limit_mode']==='user' && is_logged_in()){$c=$db->prepare('SELECT COUNT(*) c FROM responses WHERE survey_id=? AND user_id=?');$c->execute([$id,$_SESSION['user_id']]);if((int)$c->fetch()['c']>0)exit('您已提交');}

  $extra=[];$f=$db->prepare('SELECT * FROM survey_fields WHERE survey_id=?');$f->execute([$id]);
  foreach($f->fetchAll() as $field){$v=trim($_POST['field_'.$field['id']]??'');if($field['required'] && $v==='') exit('请填写'.e($field['field_label']));$extra[$field['field_name']]=$v;}

  $ins=$db->prepare('INSERT INTO responses(survey_id,user_id,ip,extra_info,created_at) VALUES(?,?,?,?,?)');
  $ins->execute([$id,$_SESSION['user_id']??null,$ip,json_encode($extra,JSON_UNESCAPED_UNICODE),now()]);
  $rid=(int)$db->lastInsertId();
  $ia=$db->prepare('INSERT INTO answers(response_id,question_id,answer_text) VALUES(?,?,?)');
  foreach($questions as $q){
    $k='q_'.$q['id'];$ans=$_POST[$k]??'';
    if(is_array($ans)) $ans=implode(',',$ans);
    if($q['required'] && trim((string)$ans)==='') exit('存在未填写必答题');
    $ia->execute([$rid,$q['id'],(string)$ans]);
  }
  if($survey['notify_admin']){
    $adm=$db->query('SELECT username FROM admins LIMIT 1')->fetch();
    send_mail('admin@example.com','问卷新提交','问卷《'.$survey['title'].'》有新提交。');
  }
  include __DIR__.'/inc/header.php';echo '<h1>感谢参与</h1>';include __DIR__.'/inc/footer.php';exit;
}

include __DIR__.'/inc/header.php';
?>
<h1><?=e($survey['title'])?></h1><p><?=nl2br(e((string)$survey['description']))?></p>
<form method="post" class="card">
<input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>">
<?php $f=$db->prepare('SELECT * FROM survey_fields WHERE survey_id=?');$f->execute([$id]);foreach($f->fetchAll() as $field):?>
<label><?=e($field['field_label'])?><input name="field_<?=$field['id']?>" <?=$field['required']?'required':''?>></label>
<?php endforeach;?>
<?php foreach($slice as $q): $opts=array_filter(array_map('trim',explode("\n",(string)$q['options_text']))); ?>
<div><strong><?=e($q['question_text'])?><?=$q['required']?' *':''?></strong>
<?php if($q['type']==='single'): foreach($opts as $o):?><label><input type="radio" name="q_<?=$q['id']?>" value="<?=e($o)?>"> <?=e($o)?></label><?php endforeach; endif;?>
<?php if($q['type']==='multi'): foreach($opts as $o):?><label><input type="checkbox" name="q_<?=$q['id']?>[]" value="<?=e($o)?>"> <?=e($o)?></label><?php endforeach; endif;?>
<?php if($q['type']==='text'):?><input name="q_<?=$q['id']?>"><?php endif;?>
<?php if($q['type']==='textarea'):?><textarea name="q_<?=$q['id']?>"></textarea><?php endif;?>
<?php if($q['type']==='select'):?><select name="q_<?=$q['id']?>"><?php foreach($opts as $o):?><option value="<?=e($o)?>"><?=e($o)?></option><?php endforeach;?></select><?php endif;?>
<?php if($q['type']==='rating'):?><input type="number" min="1" max="10" name="q_<?=$q['id']?>"><?php endif;?>
</div>
<?php endforeach;?>
<div>
<?php if($page>1):?><a class="btn" href="?id=<?=$id?>&page=<?=$page-1?>">上一页</a><?php endif;?>
<?php if($page<$total):?><a class="btn" href="?id=<?=$id?>&page=<?=$page+1?>">下一页</a><?php else:?><button>提交</button><?php endif;?>
</div>
</form>
<?php include __DIR__.'/inc/footer.php'; ?>
