<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>新增页面</title>
        <link rel="stylesheet" href="<?=__res('component/pear/css/pear.css')?>" />
        <link rel="stylesheet" href="<?=__res('admin/css/reset.css')?>" />
    </head>
    <body>

        <form class="layui-form" action="">

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
                        <label class="layui-form-label">头像</label>
                        <div class="layui-input-block">
                            <img class="img-3" src=""/>
                            <input type="text" style="display:none" name="avatar" value="<?=__res('avatar.png')?>" />
                            <button type="button" class="pear-btn pear-btn-primary pear-btn-sm" id="avatar">
                                <i class="layui-icon layui-icon-upload"></i>上传图片
                            </button>
                            <button type="button" class="pear-btn pear-btn-primary pear-btn-sm" id="attachment-choose-avatar">
                                <i class="layui-icon layui-icon-align-left"></i>选择图片
                            </button>
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
        <script src="<?=__res('admin/js/permission.js')?>"></script>
        <script>

            // 相关接口
            const INSERT_API = "<?=__url('user/insert')?>";
            
            // 字段 头像 avatar
            layui.use(["upload", "layer"], function() {
                let input = layui.$("#avatar").prev();
                input.prev().attr("src", input.val());
                layui.$("#attachment-choose-avatar").on("click", function() {
                    parent.layer.open({
                        type: 2,
                        title: "选择附件",
                        content: "<?=__url('upload/attachment?ext=jpg,jpeg,png,gif,bmp')?>",
                        area: ["95%", "90%"],
                        success: function (layero, index) {
                            parent.layui.$("#layui-layer" + index).data("callback", function (data) {
                                input.val(data.url).prev().attr("src", data.url);
                            });
                        }
                    });
                });
                layui.upload.render({
                    elem: "#avatar",
                    url: "<?=__url('upload/avatar')?>",
                    value: "<?=__res('avatar.png')?>",
                    acceptMime: "image/gif,image/jpeg,image/jpg,image/png",
                    field: "__file__",
                    done: function (res) {
                        if (res.code > 0) return layui.layer.msg(res.msg);
                        this.item.prev().val(res.data.url).prev().attr("src", res.data.url);
                    }
                });
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
                    layui.$.ajax({
                        url: INSERT_API,
                        type: "POST",
                        dateType: "json",
                        data: data.field,
                        success: function (res) {
                            if (res.code) {
                                return layui.popup.failure(res.msg);
                            }
                            return layui.popup.success("操作成功", function () {
                                parent.refreshTable();
                                parent.layer.close(parent.layer.getFrameIndex(window.name));
                            });
                        }
                    });
                    return false;
                });
            });

        </script>

    </body>
</html>
