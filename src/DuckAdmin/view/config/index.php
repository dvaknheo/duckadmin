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
                    <li>菜单配置</li>
                    <li>页面配置</li>
                </ul>
                <div class="layui-tab-content">

                    <!-- 基本信息 -->
                    <div class="layui-tab-item layui-show">

                        <form class="layui-form" lay-filter="baseInfo">
                            <div class="layui-form-item">
                                <label class="layui-form-label">网站名称</label>
                                <div class="layui-input-block">
                                    <input type="text" name="title" required  lay-verify="required" placeholder="请输入网站名称" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">网站Logo</label>
                                <div class="layui-input-block">
                                    <img class="img-3" src=""/>
                                    <input type="text" style="display:none" name="image" value="<?=__res('avatar.png')?>" />
                                    <button type="button" class="pear-btn pear-btn-primary pear-btn-sm" id="image" permission="app.admin.upload.avatar">
                                        <i class="layui-icon layui-icon-upload"></i>上传图片
                                    </button>
                                    <button type="button" class="pear-btn pear-btn-primary pear-btn-sm" id="attachment-choose-image" permission="app.admin.upload.attachment">
                                        <i class="layui-icon layui-icon-align-left"></i>选择图片
                                    </button>
                                </div>
                            </div>

                            <div class="layui-form-item">
                                <div class="layui-input-block">
                                    <button type="submit" class="pear-btn pear-btn-primary pear-btn-md" lay-submit="" lay-filter="saveBaseInfo">
                                        提交
                                    </button>
                                </div>
                            </div>
                        </form>

                    </div>

                    <!-- 菜单设置 -->
                    <div class="layui-tab-item">

                        <form class="layui-form" lay-filter="menuInfo">
                            <div class="layui-form-item">
                                <label class="layui-form-label">菜单url</label>
                                <div class="layui-input-block">
                                    <input type="text" name="data" required  lay-verify="required" placeholder="请输入菜单url" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">菜单宽度</label>
                                <div class="layui-input-block">
                                    <input type="number" name="controlWidth" required  lay-verify="required" placeholder="请输入宽度" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">默认菜单ID</label>
                                <div class="layui-input-block">
                                    <input type="number" name="select" required  lay-verify="required" placeholder="请输入默认菜单ID" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">开启手风琴</label>
                                <div class="layui-input-block">
                                    <input type="checkbox" name="accordion" id="accordion" lay-skin="switch" />
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">折叠菜单</label>
                                <div class="layui-input-block">
                                    <input type="checkbox" id="collapse" name="collapse" lay-skin="switch" />
                                </div>
                            </div>

                            <div class="layui-form-item">
                                <div class="layui-input-block">
                                    <button type="submit" class="pear-btn pear-btn-primary pear-btn-md" lay-submit="" lay-filter="saveMenuInfo">
                                        提交
                                    </button>
                                </div>
                            </div>
                        </form>

                    </div>

                    <!-- tab设置 -->
                    <div class="layui-tab-item">

                        <form class="layui-form" lay-filter="tabInfo">

                            <div class="layui-form-item">
                                <label class="layui-form-label">保持标签</label>
                                <div class="layui-input-block">
                                    <input type="checkbox" name="keepState" lay-skin="switch" />
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">记住标签</label>
                                <div class="layui-input-block">
                                    <input type="checkbox" name="session" lay-skin="switch" />
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">预加载标签</label>
                                <div class="layui-input-block">
                                    <input type="checkbox" name="preload" lay-skin="switch" />
                                </div>
                            </div>

                            <div class="layui-form-item">
                                <label class="layui-form-label">最大标签数</label>
                                <div class="layui-input-block">
                                    <input type="number" name="max" required  lay-verify="required" placeholder="请输入最大标签数" autocomplete="off" class="layui-input">
                                </div>
                            </div>

                            <div class="layui-form-item">
                                <label class="layui-form-label">主标签标题</label>
                                <div class="layui-input-block">
                                    <input type="text" name="title" required  lay-verify="required" placeholder="请输入主页标签标题" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">主标签URL</label>
                                <div class="layui-input-block">
                                    <input type="text" name="href" required  lay-verify="required" placeholder="请输入菜单url" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">主标签ID</label>
                                <div class="layui-input-block">
                                    <input type="number" name="id" required  lay-verify="required" placeholder="请输入主页标签ID" autocomplete="off" class="layui-input">
                                </div>
                            </div>

                            <div class="layui-form-item">
                                <div class="layui-input-block">
                                    <button type="submit" class="pear-btn pear-btn-primary pear-btn-md" lay-submit="" lay-filter="saveTabInfo">
                                        提交
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
        <script src="<?=__res('admin/js/permission.js')?>"></script>
        <script>

            // 基础设置
            layui.use(["upload", "layer", "popup"], function() {
                let $ = layui.$;
                let form = layui.form;

                // image
                let input = $("#image").prev();
                input.prev().attr("src", input.val());
                layui.$("#attachment-choose-image").on("click", function() {
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
                    elem: "#image",
                    url: "<?=__url('upload/avatar')?>",
                    acceptMime: "image/gif,image/jpeg,image/jpg,image/png",
                    done: function (res) {
                        if (res.code) {
                            return layui.popup.failure(res.msg);
                        }
                        this.item.prev().val(res.data.url).prev().attr("src", res.data.url);
                    }
                });

                // 提交
                form.on("submit(saveBaseInfo)", function(data){
                    $.ajax({
                        url: "<?=__url('config/update')?>",
                        dataType: "json",
                        type: "POST",
                        data: {logo: data.field},
                        success: function (res) {
                            if (res.code) {
                                return layui.popup.failure(res.msg);
                            }
                            return layui.popup.success("操作成功");
                        }
                    });
                    return false;
                });
            });

            // 菜单设置
            layui.use(["upload", "layer", "popup"], function() {
                let $ = layui.$;
                let form = layui.form;
                // 提交
                form.on("submit(saveMenuInfo)", function(data){
                    $.ajax({
                        url: "<?=__url('config/update')?>",
                        dataType: "json",
                        type: "POST",
                        data: {menu: data.field},
                        success: function (res) {
                            if (res.code) {
                                return layui.popup.failure(res.msg);
                            }
                            return layui.popup.success("操作成功");
                        }
                    });
                    return false;
                });
            });

            // 标签设置
            layui.use(["upload", "layer", "popup"], function() {
                let $ = layui.$;
                let form = layui.form;
                // 提交
                form.on("submit(saveTabInfo)", function(data){
                    let field = data.field;
                    field.index = {
                        id: field.id,
                        href: field.href,
                        title: field.title,
                    };
                    delete data.field;
                    $.ajax({
                        url: "<?=__url('config/update')?>",
                        dataType: "json",
                        type: "POST",
                        data: {tab: field},
                        success: function (res) {
                            if (res.code) {
                                return layui.popup.failure(res.msg);
                            }
                            return layui.popup.success("操作成功");
                        }
                    });
                    // 删除sessionStorage缓存
                    sessionStorage.clear();
                    return false;
                });
            });

            layui.use(["form"], function () {
                let form = layui.form;
                let $ = layui.$;
                $.ajax({
                    url: "<?=__url('config/get')?>",
                    dataType: "json",
                    success: function (res) {
                        if (res.code) {
                            return layui.popup.failure(res.msg);
                        }
                        form.val("baseInfo", res.logo);
                        $("#image").prev().val(res.logo.image).prev().attr("src", res.logo.image);
                        form.val("menuInfo", res.menu);
                        let tab = res.tab;
                        let index = tab.index;
                        delete tab.index;
                        tab.id = index.id;
                        tab.title = index.title;
                        tab.href= index.href;
                        form.val("tabInfo", res.tab);
                    }
                });

            });

        </script>

    </body>
</html>
