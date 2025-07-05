<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title></title>
        <link rel="stylesheet" href="<?=__res('component/layui/css/layui.css')?>" />
        <link rel="stylesheet" href="<?=__res('component/pear/css/pear.css')?>" />
        <link rel="stylesheet" href="<?=__res('admin/css/reset.css')?>" />
    </head>
    <body class="pear-container">
        <style>
            .layui-input-block input {
                width: 300px;
            }
        </style>

        <div class="layui-card">
            <div class="layui-card-body">

                <div class="layui-tab layui-tab-brief">
                <ul class="layui-tab-title">
                    <li class="layui-this">基本信息</li>
                    <li>安全设置</li>
                </ul>
                <div class="layui-tab-content">

                    <!-- 基本信息 -->
                    <div class="layui-tab-item layui-show">

                        <form method="post" class="layui-form" lay-filter="baseInfo" action="<?=__url('account/update')?>">
                            <div class="layui-form-item">
                                <label class="layui-form-label">昵称</label>
                                <div class="layui-input-block">
                                    <input type="text" name="nickname" required  lay-verify="required" placeholder="请输入昵称" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">邮箱</label>
                                <div class="layui-input-block">
                                    <input type="text" name="email" placeholder="请输入邮箱" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">联系电话</label>
                                <div class="layui-input-block">
                                    <input type="text" name="mobile" placeholder="请输入联系电话" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <div class="layui-input-block">
                                    <button type="submit" class="pear-btn pear-btn-primary pear-btn-md" lay-submit="" lay-filter="saveBaseInfo">
                                        提交
                                    </button>
                                    <button type="reset" class="pear-btn pear-btn-md">
                                        重置
                                    </button>
                                </div>
                            </div>
                        </form>

                    </div>

                    <div class="layui-tab-item">

                        <form method="post" class="layui-form" action="<?=__url('account/password')?>">
                            <div class="layui-form-item">
                                <label class="layui-form-label">原始密码</label>
                                <div class="layui-input-block">
                                    <input type="password" name="old_password" required  lay-verify="required" placeholder="请输入原始密码" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">新密码</label>
                                <div class="layui-input-block">
                                    <input type="password" name="password" required  lay-verify="required" placeholder="请输入新密码" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">确认新密码</label>
                                <div class="layui-input-block">
                                    <input type="password" name="password_confirm" required  lay-verify="required" placeholder="请再次输入新密码" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <div class="layui-input-block">
                                    <button type="submit" class="pear-btn pear-btn-primary pear-btn-md" lay-submit="" lay-filter="savePassword">
                                        提交
                                    </button>
                                    <button type="reset" class="pear-btn pear-btn-md">
                                        重置
                                    </button>
                                </div>
                            </div>
                        </form>

                    </div>

                </div>
            </div>

            </div>
        </div>
        <script src="<?=__res('component/layui/layui.js')?>"></script>
        <script src="<?=__res('component/pear/pear.js')?>"></script>
        <script src="<?=__res('admin/js/common.js')?>"></script>
<script>
<?php // 这段js 存放 动态数据 ?>
var data_permission = "<?=__url('rule/permission')?>";
var data_of_this= "<?=__url('account/info')?>";
</script>
<script>
layui.use(["form", "popup"], function () {
    togglePermission(data_permission);
    var url = data_of_this;
    fetch_data_and_run(url, function(data){
        layui.form.val("baseInfo", data);
    })
    layui.form.on("submit(saveBaseInfo)", function(data){
        ajax_post(this.closest('form'),function (res) {
                if (res.code) {
                    return layui.popup.failure(res.msg);
                }
                return layui.popup.success("操作成功");
        });
        return false;
    });

    layui.form.on("submit(savePassword)", function(data){
        ajax_post(this.closest('form'),function (res) {
                if (res.code) {
                    return layui.popup.failure(res.msg);
                }
                return layui.popup.success("操作成功");
        });
        return false;
    });

});
</script>
    </body>
</html>
