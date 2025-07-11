
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>更新页面</title>
        <link rel="stylesheet" href="<?=__res('component/pear/css/pear.css')?>" />
        <link rel="stylesheet" href="<?=__res('admin/css/reset.css')?>" />
        <style>
            .layui-iconpicker .layui-anim {
                bottom: 42px !important;
                top: inherit !important;
            }
        </style>
    </head>
    <body>

        <form method="post" class="layui-form">
            <input type="hidden" name="id" value="<?= $_GET['id']?>">
            <div class="mainBox">
                <div class="main-container mr-5">

                    <div class="layui-form-item">
                        <label class="layui-form-label required">标题</label>
                        <div class="layui-input-block">
                            <input type="text" name="title" required lay-verify="required" value="" class="layui-input">
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <label class="layui-form-label required">标识</label>
                        <div class="layui-input-block">
                            <input type="text" name="key" required lay-verify="required" value="" class="layui-input">
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <label class="layui-form-label">上级菜单</label>
                        <div class="layui-input-block">
                            <div name="pid" id="pid" value="0" ></div>
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <label class="layui-form-label">url</label>
                        <div class="layui-input-block">
                            <input type="text" name="href" value="" class="layui-input">
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <label class="layui-form-label">图标</label>
                        <div class="layui-input-block">
                            <input name="icon" id="icon" />
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <label class="layui-form-label">类型</label>
                        <div class="layui-input-block">
                            <div name="type" id="type" value="1" ></div>
                        </div>
                    </div>

                    <div class="layui-form-item">
                        <label class="layui-form-label">排序</label>
                        <div class="layui-input-block">
                            <input type="number" name="weight" value="0" class="layui-input">
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
var date_rule_tree = "<?=__url('rule/select?format=tree&type=0,1')?>";
var data_of_this = "<?=__url('rule/select')?>" + location.search; // 这里要改成从后端获取数据
</script>
<script>
layui.use(["form", "util", "jquery", "xmSelect","popup","iconPicker"], function () {
    togglePermission(data_permission);
    
    var url = data_of_this;
    fetch_data_and_run(url, function(data){
        // 赋值表单
        fill_form(data[0]);

        // 图标选择
        layui.iconPicker.render({
            elem: "#icon",
            type: "fontClass",
            page: false,
        });
        // 菜单类型下拉选择
        var initValue = element_split_value('#type');
        layui.xmSelect.render({
            el: "#type",
            name: "type",
            initValue: initValue,
            data: [{"value":"0","name":"目录"},{"value":"1","name":"菜单"},{"value":"2","name":"权限"}],
            model: {"icon":"hidden","label":{"type":"text"}},
            clickClose: true,
            radio: true,
        })
        
        // 获取上级菜单
        var url = date_rule_tree;
        fetch_data_and_run(url, function(data){
                var initValue = element_split_value('#pid');
                layui.xmSelect.render({
                    el: "#pid",
                    name: "pid",
                    initValue: initValue,
                    tips: "无",
                    toolbar: {show: true, list: ["CLEAR"]},
                    data: data,
                    model: {"icon":"hidden","label":{"type":"text"}},
                    clickClose: true,
                    radio: true,
                    tree: {show: true,"strict":false,"clickCheck":true,"clickExpand":false,expandedKeys: initValue},
                });
        });
    });
    // 表单提交事件
    layui.form.on("submit(save)", function (data) {
        return app_ajax_post(this);
    });
});
</script>

    </body>
</html>
