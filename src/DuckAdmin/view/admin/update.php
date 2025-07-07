<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>更新页面</title>
        <link rel="stylesheet" href="<?=__res('component/pear/css/pear.css')?>" />
        <link rel="stylesheet" href="<?=__res('admin/css/reset.css')?>" />
    </head>
    <body>

        <form method="post" class="layui-form">
            <input type="hidden" name="id" value="<?=$_GET['id']/*安全问题*/?>">
            <div class="mainBox">
                <div class="main-container mr-5">

                    <div class="layui-form-item">
                        <label class="layui-form-label required">角色</label>
                        <div class="layui-input-block">
                            <div name="roles" id="roles" value=""></div>
                        </div>
                    </div>
                    
                    <div class="layui-form-item">
                        <label class="layui-form-label required">用户名</label>
                        <div class="layui-input-block">
                            <input type="text" name="username" value="" required lay-verify="required" class="layui-input">
                        </div>
                    </div>
                    
                    <div class="layui-form-item">
                        <label class="layui-form-label required">昵称</label>
                        <div class="layui-input-block">
                            <input type="text" name="nickname" value="" required lay-verify="required" class="layui-input">
                        </div>
                    </div>
                    
                    <div class="layui-form-item">
                        <label class="layui-form-label">密码</label>
                        <div class="layui-input-block">
                            <input type="text" name="password" value="" class="layui-input">
                        </div>
                    </div>
                    
                    <div class="layui-form-item">
                        <label class="layui-form-label">邮箱</label>
                        <div class="layui-input-block">
                            <input type="text" name="email" value="" class="layui-input">
                        </div>
                    </div>
                    
                    <div class="layui-form-item">
                        <label class="layui-form-label">手机</label>
                        <div class="layui-input-block">
                            <input type="text" name="mobile" value="" class="layui-input">
                        </div>
                    </div>
                    
                </div>
            </div>

            <div class="bottom">
                <div class="button-container">
                    <button type="submit" class="pear-btn pear-btn-primary pear-btn-md" lay-submit=""
                        lay-filter="save">
                        提交
                    </button>
                    <button type="reset" class="pear-btn pear-btn-md">
                        重置
                    </button>
                </div>
            </div>
            
        </form>

        <script src="<?=__res('component/layui/layui.js')?>"></script>
        <script src="<?=__res('component/pear/pear.js')?>"></script>
        <script src="<?=__res('admin/js/common.js')?>"></script>
<script>
<?php // 这段js 存放 动态数据 ?>
var data_permission = "<?=__url('rule/permission')?>";
var data_role_tree = "<?=__url('role/select?format=tree')?>"
var data_of_this = "<?=__url('admin/select')?>" + location.search;
var isSupperAdmin = true; //TODO
</script>
<script>
layui.use(["form", "jquery","util","xmSelect", "popup"], function () {
    togglePermission(data_permission);
    
    var url = data_of_this;
    fetch_data_and_run(url, function(data){
        // 给表单初始化数据
        fill_form(data[0]);
        
        // 字段 角色 roles
        var url = data_role_tree;
        fetch_data_and_run(url, function(data){
            if (!isSupperAdmin) {
                layui.each(data, function (k, v) {
                    v.disabled = true;
                });
            }
            var initValue = element_split_value('#roles');
            layui.xmSelect.render({
                el: "#roles",
                name: "roles",
                initValue: initValue,
                data: data,
                layVerify: "required",
                tree: {show: true, expandedKeys: true, strict: false},
                toolbar: {show: true, list: ["ALL","CLEAR","REVERSE"]},
            });
        });
    });
    layui.form.on("submit(save)", function (data) {
        return app_ajax_post(this);
    });
});
</script>
    </body>
</html>
