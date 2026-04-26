<?php
require __DIR__ . '/inc/bootstrap.php';
$err='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  verify_csrf();
  if(login_user(trim($_POST['email']??''), $_POST['password']??'')) redirect('/user/index.php');
  $err='登录失败，请检查账号状态。';
}
include __DIR__.'/inc/header.php'; ?>
<h1>用户登录</h1><?php if($err):?><p class="error"><?=e($err)?></p><?php endif;?>
<form method="post" class="card">
<input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>">
<label>邮箱<input name="email" type="email" required></label>
<label>密码<input name="password" type="password" required></label>
<button>登录</button>
</form>
<?php include __DIR__.'/inc/footer.php';
