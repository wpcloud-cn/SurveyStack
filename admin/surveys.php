<?php
require __DIR__ . '/../inc/bootstrap.php';
require_admin();
if($_SERVER['REQUEST_METHOD']==='POST'){
  verify_csrf();
  if(($_POST['action']??'')==='create'){
    $db->prepare('INSERT INTO surveys(title,description,start_at,end_at,require_login,page_size,limit_mode,anonymous_allowed,notify_admin,is_public,created_at) VALUES(?,?,?,?,?,?,?,?,?,?,?)')
      ->execute([trim($_POST['title']),trim($_POST['description']),$_POST['start_at']?:null,$_POST['end_at']?:null,!empty($_POST['require_login'])?1:0,(int)$_POST['page_size'],$_POST['limit_mode']??'none',!empty($_POST['anonymous_allowed'])?1:0,!empty($_POST['notify_admin'])?1:0,1,now()]);
  }
  if(($_POST['action']??'')==='delete'){$db->prepare('DELETE FROM surveys WHERE id=?')->execute([(int)$_POST['id']]);}
}
$rows=$db->query('SELECT * FROM surveys ORDER BY id DESC')->fetchAll();
include __DIR__ . '/../inc/header.php'; ?>
<h1>问卷管理</h1>
<form method="post" class="card">
<input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>"><input type="hidden" name="action" value="create">
<label>标题<input name="title" required></label>
<label>说明<textarea name="description"></textarea></label>
<label>开始时间<input type="datetime-local" name="start_at"></label>
<label>结束时间<input type="datetime-local" name="end_at"></label>
<label>每页题数<input name="page_size" value="10" type="number"></label>
<label>限制<select name="limit_mode"><option value="none">不限制</option><option value="ip">限制IP</option><option value="user">限制用户</option></select></label>
<label><input type="checkbox" name="require_login" value="1">仅登录用户填写</label>
<label><input type="checkbox" name="anonymous_allowed" value="1" checked>允许匿名</label>
<label><input type="checkbox" name="notify_admin" value="1">提交后通知管理员</label>
<button>创建问卷</button></form>
<?php foreach($rows as $r):?><div class="card"><strong><?=e($r['title'])?></strong>
<a class="btn" href="/admin/questions.php?survey_id=<?=$r['id']?>">题目管理</a>
<form method="post" style="display:inline"><input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?=$r['id']?>"><button onclick="return confirm('确定删除?')">删除</button></form>
</div><?php endforeach;?>
<nav><a href="/admin/results.php">结果</a> | <a href="/admin/users.php">用户</a> | <a href="/admin/settings.php">设置</a> | <a href="/logout.php">退出</a></nav>
<?php include __DIR__ . '/../inc/footer.php'; ?>
