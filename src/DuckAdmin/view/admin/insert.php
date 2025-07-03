<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>新增页面</title>
        <link rel="stylesheet" href="<?=__res('component/pear/css/pear.css')?>" />
        <link rel="stylesheet" href="<?=__res('admin/css/reset.css')?>" />
    </head>
    <body>

        <form method="post" class="layui-form">

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
                        <label class="layui-form-label required">密码</label>
                        <div class="layui-input-block">
                            <input type="text" name="password" value="" required lay-verify="required" class="layui-input">
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
<script>
    window.PERMISSION_API = "<?=__url('rule/permission')?>";
</script>
        <script src="<?=__res('component/layui/layui.js')?>"></script>
        <script src="<?=__res('component/pear/pear.js')?>"></script>
        <script src="<?=__res('admin/js/common.js')?>"></script>
        <script>
layui.$(function () {
    togglePermission();
});
            // 字段 角色 roles
            layui.use(["jquery", "xmSelect", "popup"], function() {
                layui.$.ajax({
                    url: "<?=__url('role/select?format=tree')?>",
                    dataType: "json",
                    success: function (res) {
                        let value = layui.$("#roles").attr("value");
                        let initValue = value ? value.split(",") : [];
                        if (!top.Admin.Account.isSupperAdmin) {
                            layui.each(res.data, function (k, v) {
                                v.disabled = true;
                            });
                        }
                        layui.xmSelect.render({
                            el: "#roles",
                            name: "roles",
                            initValue: initValue,
                            data: res.data,
                            layVerify: "required",
                            tree: {"show":true, expandedKeys:true, strict:false},
                            toolbar: {show:true, list:["ALL","CLEAR","REVERSE"]},
                        });
                        if (res.code) {
                            layui.popup.failure(res.msg);
                        }
                    }
                });
            });
            
            //提交事件
            layui.use(["form", "popup"], function () {
                layui.form.on("submit(save)", function (data) {
                    ajax_post(this.closest('form'),function (res) {
                            if (res.code) {
                                return layui.popup.failure(res.msg);
                            }
                            return layui.popup.success("操作成功", function () {
                                parent.refreshTable();
                                parent.layer.close(parent.layer.getFrameIndex(window.name));
                            });
                    });
                    return false;
                });
            });

        </script>

    </body>
</html>
