<?php
require __DIR__ . '/inc/bootstrap.php';
$msg='';$err='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  verify_csrf();
  $username=trim($_POST['username']??'');
  $email=trim($_POST['email']??'');
  $password=$_POST['password']??'';
  if(!$username||!filter_var($email,FILTER_VALIDATE_EMAIL)||strlen($password)<6){$err='参数不合法';}
  else{
    $token=bin2hex(random_bytes(24));
    $hash=password_hash($password,PASSWORD_DEFAULT);
    try{
      $stmt=$db->prepare('INSERT INTO users(username,email,password_hash,verify_token,created_at) VALUES(?,?,?,?,?)');
      $stmt->execute([$username,$email,$hash,$token,now()]);
      $link=app_url('/verify.php?token='.$token);
      send_mail($email,'账户激活','请点击激活：<a href="'.$link.'">'.$link.'</a>');
      $msg='注册成功，请检查邮箱激活。';
    }catch(Throwable $e){$err='邮箱已存在或系统错误';}
  }
}
include __DIR__.'/inc/header.php'; ?>
<h1>用户注册</h1><?php if($msg):?><p class="ok"><?=e($msg)?></p><?php endif;?><?php if($err):?><p class="error"><?=e($err)?></p><?php endif;?>
<form method="post" class="card">
<input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>">
<label>用户名<input name="username" required></label>
<label>邮箱<input name="email" type="email" required></label>
<label>密码<input name="password" type="password" required></label>
<button>注册</button>
</form>
<?php include __DIR__.'/inc/footer.php'; ?>
