<?php
require __DIR__ . '/inc/bootstrap.php';
$token=$_GET['token']??'';
if($token){$stmt=$db->prepare('UPDATE users SET is_verified=1,verify_token=NULL WHERE verify_token=?');$stmt->execute([$token]);}
include __DIR__.'/inc/header.php';
?><h1>激活结果</h1><p><?= $stmt->rowCount()?'激活成功，请登录。':'链接无效或已使用。' ?></p><?php include __DIR__.'/inc/footer.php';
