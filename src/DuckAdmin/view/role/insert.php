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
                        <label class="layui-form-label">父级</label>
                        <div class="layui-input-block">
                            <div name="pid" id="pid" value="1" ></div>
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <label class="layui-form-label required">角色名</label>
                        <div class="layui-input-block">
                            <input type="text" name="name" value="" required lay-verify="required" class="layui-input">
                        </div>
                    </div>
                    
                    <div class="layui-form-item">
                        <label class="layui-form-label">权限</label>
                        <div class="layui-input-block">
                            <div name="rules" id="rules" value="" ></div>
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
        <script src="<?=__res('admin/js/permission.js')?>"></script>
        
        <script>

            // 相关接口
            const INSERT_API = "<?=__url('role/insert')?>";
            
            // 字段 权限 rules
            layui.use(["jquery", "xmSelect", "popup"], function() {
                layui.$.ajax({
                    url: "<?=__url('role/rules?id=1')?>",
                    dataType: "json",
                    success: function (res) {
                        let value = layui.$("#rules").attr("value");
                        let initValue = value ? value.split(",") : [];
                        layui.xmSelect.render({
                            el: "#rules",
                            name: "rules",
                            initValue: initValue,
                            data: res.data,
                            tree: {"show":true,expandedKeys:initValue},
                            toolbar: {show:true,list:["ALL","CLEAR","REVERSE"]},
                        })
                    }
                });
            });
            
            // 字段 父级 pid
            layui.use(["jquery", "xmSelect", "popup"], function() {
                layui.$.ajax({
                    url: "<?=__url('role/select?format=tree')?>",
                    dataType: "json",
                    success: function (res) {
                        let value = layui.$("#pid").attr("value");
                        let initValue = value ? value.split(",") : [];
                        layui.xmSelect.render({
                            el: "#pid",
                            name: "pid",
                            initValue: initValue,
                            tips: "请选择",
                            data: res.data,
                            value: "0",
                            model: {"icon":"hidden","label":{"type":"text"}},
                            clickClose: true,
                            radio: true,
                            tree: {show: true,"strict":false,"clickCheck":true,"clickExpand":false,expandedKeys:true},
                            on: function(data){
                                let id = data.arr[0] ? data.arr[0].value : "";
                                if (!id) return;
                                layui.$.ajax({
                                    url: '<?=__url('role/rules?id=')?>' + id,
                                    dataType: 'json',
                                    success: function (res) {
                                        if (res.code) {
                                            return layui.popup.failure(res.msg);
                                        }
                                        layui.xmSelect.get('#rules')[0].update({data:res.data});
                                    }
                                });
                            }
                        })
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
