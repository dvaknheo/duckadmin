<?php declare(strict_types=1);
if (isset($__view_data['header'])) {
    echo $__view_data['header'];
}
?>
欢迎你 【<?= $user['username']?>】
<?=$error?>
<form method="post">
    <label>旧密码<input type="password" name="oldpassword"/></label><br />
    <label>新密码<input type="password" name="newpassword"/></label><br />
    <label>重复新密码<input type="password" name="newpassword_confirm"/></label><br />
    <label><input type="submit" value="提交" /></label><br />
</form>

<?php
if (isset($__view_data['footer'])) {
    echo $__view_data['footer'];
}
?>