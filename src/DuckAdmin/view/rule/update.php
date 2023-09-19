
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

        <form class="layui-form">

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
                    <button type="submit" class="pear-btn pear-btn-primary pear-btn-md" lay-submit="" lay-filter="save">
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
            let PRIMARY_KEY = "id";
            const SELECT_API = "<?=__url('rule/select')?>" + location.search;
            const UPDATE_API = "<?=__url('rule/update')?>";

            // 获取行数据
            layui.use(["form", "util", "popup"], function () {
                let $ = layui.$;
                $.ajax({
                    url: SELECT_API,
                    dataType: "json",
                    success: function (res) {

                        // 赋值表单
                        layui.each(res.data[0], function (key, value) {
                            let obj = $('*[name="'+key+'"]');
                            if (key === "password") {
                                obj.attr("placeholder", "不更新密码请留空");
                                return;
                            }
                            if (typeof obj[0] === "undefined" || !obj[0].nodeName) return;
                            if (obj[0].nodeName.toLowerCase() === "textarea") {
                                obj.html(layui.util.escape(value));
                            } else {
                                obj.attr("value", value);
                            }
                        });

                        // 图标选择
                        layui.use(["iconPicker"], function() {
                            layui.iconPicker.render({
                                elem: "#icon",
                                type: "fontClass",
                                page: false,
                            });
                        });

                        // 获取上级菜单
                        layui.use(["jquery", "xmSelect", "popup"], function() {
                            layui.$.ajax({
                                url: "<?=__url('rule/select')?>?format=tree&type=0,1",
                                dataType: "json",
                                success: function (res) {
                                    let value = layui.$("#pid").attr("value");
                                    let initValue = value ? value.split(",") : [];
                                    layui.xmSelect.render({
                                        el: "#pid",
                                        name: "pid",
                                        initValue: initValue,
                                        tips: "无",
                                        toolbar: {show: true, list: ["CLEAR"]},
                                        data: res.data,
                                        model: {"icon":"hidden","label":{"type":"text"}},
                                        clickClose: true,
                                        radio: true,
                                        tree: {show: true,"strict":false,"clickCheck":true,"clickExpand":false,expandedKeys: initValue},
                                    });
                                    if (res.code) {
                                        return layui.popup.failure(res.msg);
                                    }
                                }
                            });
                        });

                        // 菜单类型下拉选择
                        layui.use(["jquery", "xmSelect"], function() {
                            let value = layui.$("#type").attr("value");
                            let initValue = value ? value.split(",") : [];
                            layui.xmSelect.render({
                                el: "#type",
                                name: "type",
                                initValue: initValue,
                                data: [{"value":"0","name":"目录"},{"value":"1","name":"菜单"},{"value":"2","name":"权限"}],
                                model: {"icon":"hidden","label":{"type":"text"}},
                                clickClose: true,
                                radio: true,
                            })
                        });

                        // ajax产生错误
                        if (res.code) {
                            layui.popup.failure(res.msg);
                        }

                    }
                });
            });

            // 提交事件
            layui.use(["form", "popup"], function () {
                layui.form.on("submit(save)", function (data) {
                    data.field[PRIMARY_KEY] = layui.url().search[PRIMARY_KEY];
                    layui.$.ajax({
                        url: UPDATE_API,
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
