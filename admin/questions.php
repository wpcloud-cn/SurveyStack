<?php
require __DIR__ . '/../inc/bootstrap.php';
require_admin();
$surveyId=(int)($_GET['survey_id']??0);
if($_SERVER['REQUEST_METHOD']==='POST'){
 verify_csrf();
 if(($_POST['action']??'')==='addq'){
  $db->prepare('INSERT INTO questions(survey_id,question_text,type,options_text,required,jump_logic,sort_order) VALUES(?,?,?,?,?,?,?)')
   ->execute([$surveyId,trim($_POST['question_text']),$_POST['type'],trim($_POST['options_text']),!empty($_POST['required'])?1:0,trim($_POST['jump_logic']),(int)$_POST['sort_order']]);
 }
 if(($_POST['action']??'')==='delq'){$db->prepare('DELETE FROM questions WHERE id=? AND survey_id=?')->execute([(int)$_POST['id'],$surveyId]);}
 if(($_POST['action']??'')==='addf'){$db->prepare('INSERT INTO survey_fields(survey_id,field_name,field_label,required) VALUES(?,?,?,?)')->execute([$surveyId,trim($_POST['field_name']),trim($_POST['field_label']),!empty($_POST['required'])?1:0]);}
}
$questions=$db->prepare('SELECT * FROM questions WHERE survey_id=? ORDER BY sort_order,id');$questions->execute([$surveyId]);
$fields=$db->prepare('SELECT * FROM survey_fields WHERE survey_id=? ORDER BY id');$fields->execute([$surveyId]);
include __DIR__ . '/../inc/header.php'; ?>
<h1>题目管理</h1>
<form method="post" class="card"><input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>"><input type="hidden" name="action" value="addf">
<h3>添加额外信息字段</h3><label>字段名<input name="field_name"></label><label>显示名<input name="field_label"></label><label><input type="checkbox" name="required" value="1">必填</label><button>添加字段</button></form>
<?php foreach($fields->fetchAll() as $f):?><div><?=e($f['field_label'])?> (<?=e($f['field_name'])?>)</div><?php endforeach;?>
<form method="post" class="card"><input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>"><input type="hidden" name="action" value="addq">
<label>题目内容<textarea name="question_text"></textarea></label>
<label>类型<select name="type"><option value="single">单选</option><option value="multi">多选</option><option value="text">单行文本</option><option value="textarea">多行文本</option><option value="select">下拉</option><option value="rating">评分</option></select></label>
<label>选项(每行一个)<textarea name="options_text"></textarea></label>
<label>跳题逻辑(JSON，如 {"是":5,"否":7})<input name="jump_logic"></label>
<label>排序<input type="number" name="sort_order" value="0"></label>
<label><input type="checkbox" name="required" value="1">必答</label><button>添加题目</button></form>
<?php foreach($questions->fetchAll() as $q):?><div class="card"><strong><?=e($q['question_text'])?></strong> [<?=e($q['type'])?>]
<form method="post" style="display:inline"><input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>"><input type="hidden" name="action" value="delq"><input type="hidden" name="id" value="<?=$q['id']?>"><button>删除</button></form>
</div><?php endforeach;?>
<?php include __DIR__ . '/../inc/footer.php'; ?>
