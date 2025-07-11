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
        <script src="<?=__res('component/layui/layui.js')?>"></script>
        <script src="<?=__res('component/pear/pear.js')?>"></script>
        <script src="<?=__res('admin/js/common.js')?>"></script>
<script>
<?php // 这段js 存放 动态数据 ?>
var data_permission = "<?=__url('rule/permission')?>";
var data_role_tree = "<?=__url('role/select?format=tree')?>";
var url_role_rule = "<?=__url('role/rules?id=')?>";
</script>
<script>
layui.use(["form", "popup","jquery", "xmSelect", "util"], function () {
    togglePermission(data_permission);
    fetch_data_and_run(null, function(){
            // 字段 权限 rules
            var id = 1;
            var url = url_role_rule + pid;
            fetch_data_and_run(url, function(data){
                    var initValue = element_split_value('#rules');
                    //data = res.data
                    layui.xmSelect.render({
                        el: "#rules",
                        name: "rules",
                        initValue: initValue,
                        data: data,
                        tree: {"show":true,expandedKeys:initValue},
                        toolbar: {show:true,list:["ALL","CLEAR","REVERSE"]},
                    })
            });
            
            // 字段 父级角色组 pid
            var url = data_role_tree;
            fetch_data_and_run(url, function(data){
                    var initValue = element_split_value('#pid');
                    layui.xmSelect.render({
                        el: "#pid",
                        name: "pid",
                        initValue: initValue,
                        tips: "请选择",
                        data: data,
                        value: "0",
                        model: {"icon":"hidden","label":{"type":"text"}},
                        clickClose: true,
                        radio: true,
                        tree: {show: true,"strict":false,"clickCheck":true,"clickExpand":false,expandedKeys:true},
                        on: function(data){
                            let id = data.arr[0] ? data.arr[0].value : "";
                            if (!id) return;
                            var url = url_role_rule + id;
                            fetch_data_and_run(url, function(data){
                                    layui.xmSelect.get('#rules')[0].update({data:data});
                            });
                        }
                    });
            });
    });
    //提交事件
    layui.form.on("submit(save)", function (data) {
        return app_ajax_post(this);
    });
});
</script>

    </body>

</html>
