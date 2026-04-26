<?php
require __DIR__ . '/../inc/bootstrap.php';
require_admin();
$msg='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 verify_csrf();
 foreach($_POST as $k=>$v){if($k==='csrf_token')continue;$db->prepare('UPDATE settings SET config_value=? WHERE config_key=?')->execute([(string)$v,$k]);}
 $msg='已保存';
}
$rows=$db->query('SELECT config_key,config_value FROM settings')->fetchAll();$s=[];foreach($rows as $r){$s[$r['config_key']]=$r['config_value'];}
include __DIR__ . '/../inc/header.php'; ?>
<h1>站点设置</h1><?php if($msg):?><p class="ok"><?=e($msg)?></p><?php endif;?>
<form method="post" class="card"><input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>">
<label>站点名称<input name="site_name" value="<?=e($s['site_name']??'')?>"></label>
<label>Logo 文本<input name="site_logo_text" value="<?=e($s['site_logo_text']??'')?>"></label>
<label>底部版权<input name="footer_text" value="<?=e($s['footer_text']??'')?>"></label>
<h3>SMTP</h3>
<label>服务器<input name="smtp_host" value="<?=e($s['smtp_host']??'')?>"></label>
<label>端口<input name="smtp_port" value="<?=e($s['smtp_port']??'587')?>"></label>
<label>账号<input name="smtp_user" value="<?=e($s['smtp_user']??'')?>"></label>
<label>密码<input type="password" name="smtp_pass" value="<?=e($s['smtp_pass']??'')?>"></label>
<label>加密<select name="smtp_secure"><option value="tls">TLS</option><option value="ssl" <?=($s['smtp_secure']??'')==='ssl'?'selected':''?>>SSL</option></select></label>
<h3>邮件模板</h3>
<label>激活邮件<textarea name="mail_tpl_activation"><?=e($s['mail_tpl_activation']??'')?></textarea></label>
<label>重置邮件<textarea name="mail_tpl_reset"><?=e($s['mail_tpl_reset']??'')?></textarea></label>
<button>保存设置</button></form>
<?php include __DIR__ . '/../inc/footer.php'; ?>
