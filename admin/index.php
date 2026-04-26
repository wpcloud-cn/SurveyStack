<?php
require __DIR__ . '/../inc/bootstrap.php';
$err='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  verify_csrf();
  if(login_admin(trim($_POST['username']??''),$_POST['password']??'')) redirect('/admin/surveys.php');
  $err='管理员账号错误';
}
if(is_admin()) redirect('/admin/surveys.php');
include __DIR__ . '/../inc/header.php'; ?>
<h1>后台登录</h1><?php if($err):?><p class="error"><?=e($err)?></p><?php endif;?>
<form method="post" class="card"><input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>"><label>用户名<input name="username"></label><label>密码<input type="password" name="password"></label><button>登录</button></form>
<?php include __DIR__ . '/../inc/footer.php'; ?>
