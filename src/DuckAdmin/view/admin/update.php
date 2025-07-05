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
var URL_ROLE_TREE = "<?=__url('role/select?format=tree')?>"
const SELECT_API = "<?=__url('admin/select')?>" + location.search;
// 获取数据库记录
layui.use(["form", "jquery","util","xmSelect", "popup"], function () {
    togglePermission("<?=__url('rule/permission')?>");
    
    var url = SELECT_API;
    fetch_data_and_run(url, function(data){
        // 给表单初始化数据
        fill_form(data[0]);
        
        // 字段 角色 roles
        var url = URL_ROLE_TREE;
        fetch_data_and_run(url, function(data){
            let value = layui.$("#roles").attr("value");
            let initValue = value ? value.split(",") : [];
            if (!top.Admin.Account.isSupperAdmin) {
                layui.each(data, function (k, v) {
                    v.disabled = true;
                });
            }
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
