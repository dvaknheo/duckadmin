<div>
    <p><label><?=__hl('Admin Name')?>: <input type="text" name="admin_name" value="<?=__h((string)($post['admin_name'] ?? ''))?>"></label></p>
    <p><label><?=__hl('Admin Password')?>: <input type="password" name="admin_password" value="<?=__h((string)($post['admin_password'] ?? ''))?>"></label></p>
    <p><label><?=__hl('Admin Password Confirm')?>: <input type="password" name="admin_password_confirm" value="<?=__h((string)($post['admin_password_confirm'] ?? ''))?>"></label></p>
</div>

