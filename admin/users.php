<?php
require __DIR__ . '/../inc/bootstrap.php';
require_admin();
if($_SERVER['REQUEST_METHOD']==='POST'){
 verify_csrf();
 $id=(int)$_POST['id'];
 if($_POST['action']==='toggle'){$db->prepare('UPDATE users SET status=CASE WHEN status=1 THEN 0 ELSE 1 END WHERE id=?')->execute([$id]);}
 if($_POST['action']==='reset'){ $hash=password_hash('12345678',PASSWORD_DEFAULT);$db->prepare('UPDATE users SET password_hash=? WHERE id=?')->execute([$hash,$id]); }
}
$users=$db->query('SELECT * FROM users ORDER BY id DESC')->fetchAll();
include __DIR__ . '/../inc/header.php'; ?>
<h1>用户管理</h1>
<?php foreach($users as $u):?><div class="card">#<?=$u['id']?> <?=e($u['username'])?> (<?=e($u['email'])?>) 状态:<?=$u['status']?'启用':'禁用'?>
<form method="post" style="display:inline"><input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>"><input type="hidden" name="id" value="<?=$u['id']?>"><input type="hidden" name="action" value="toggle"><button>启用/禁用</button></form>
<form method="post" style="display:inline"><input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>"><input type="hidden" name="id" value="<?=$u['id']?>"><input type="hidden" name="action" value="reset"><button>重置密码</button></form>
</div><?php endforeach;?>
<?php include __DIR__ . '/../inc/footer.php'; ?>
