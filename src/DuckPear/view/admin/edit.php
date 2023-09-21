<form method = "post" class="layui-form" action="">
    <input type="hidden" name="id" value="<?=$admin['id']?>">
    <div class="mainBox">
        <div class="main-container">
            <div class="layui-form-item">
                <label class="layui-form-label">用户名</label>
                <div class="layui-input-block">
                    <input type="text" maxlength="30" name="username" value="<?=__h($admin['username'])?>"" lay-verify="required" placeholder="请输入用户名" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">昵称</label>
                <div class="layui-input-block">
                    <input type="text"  name="nickname" value="<?=__h($admin['nickname'])?>" placeholder="请输入昵称"  autocomplete="off"  class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">职位</label>
                <div class="layui-input-block">
                    <select name="role_id" lay-verify="">
<?php
foreach($roles as $k => $v){
?>
                    <option value="<?=$k?>" <?php $k==$admin['role_id']?'selected':''?> ><?=__h($v)?></option>
<?php
}
?>
                    </select>     
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">密码</label>
                <div class="layui-input-block">
                    <input type="password" name="password"  placeholder="请输入密码,留空不修改" class="layui-input">
                </div>
            </div>
        </div>
    </div>
    <div class="bottom">
        <div class="button-container">
            <button type="submit" class="layui-btn layui-btn-normal layui-btn-sm" lay-submit="" lay-filter="save">
                <i class="layui-icon layui-icon-ok"></i>
                提交
            </button>
            <button type="reset" class="layui-btn layui-btn-primary layui-btn-sm">
                <i class="layui-icon layui-icon-refresh"></i>
                重置
            </button>
        </div>
    </div>
</form>
