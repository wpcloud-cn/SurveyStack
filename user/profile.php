<?php
require __DIR__ . '/../inc/bootstrap.php';
require_login();
$u=current_user();$msg='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  verify_csrf();
  $name=trim($_POST['username']??'');
  $db->prepare('UPDATE users SET username=? WHERE id=?')->execute([$name,$_SESSION['user_id']]);
  $msg='已保存';$u=current_user();
}
include __DIR__ . '/../inc/header.php'; ?>
<h1>修改资料</h1><?php if($msg):?><p class="ok"><?=e($msg)?></p><?php endif;?>
<form method="post" class="card"><input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>"><label>用户名<input name="username" value="<?=e($u['username'])?>"></label><button>保存</button></form>
<?php include __DIR__ . '/../inc/footer.php'; ?>
