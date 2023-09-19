
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title></title>
        <link rel="stylesheet" href="<?=__res('component/pear/css/pear.css')?>" />
        <link rel="stylesheet" href="<?=__res('admin/css/reset.css')?>" />
    </head>
    <body class="pear-container">

        <!-- 数据表格 -->
        <div class="layui-card">
            <div class="layui-card-body">
                <table id="data-table" lay-filter="data-table"></table>
            </div>
        </div>

        <!-- 表格顶部工具栏 -->
        <script type="text/html" id="table-toolbar">
            <button class="pear-btn pear-btn-primary pear-btn-md" lay-event="add" permission="app.admin.rule.insert">
                <i class="layui-icon layui-icon-add-1"></i>新增
            </button>
            <button class="pear-btn pear-btn-danger pear-btn-md" lay-event="batchRemove" permission="app.admin.rule.delete">
                <i class="layui-icon layui-icon-delete"></i>删除
            </button>
        </script>

        <!-- 表格行工具栏 -->
        <script type="text/html" id="table-bar">
            <button class="pear-btn pear-btn-xs tool-btn" lay-event="edit" permission="app.admin.rule.update">编辑</button>
            <button class="pear-btn pear-btn-xs tool-btn" lay-event="remove" permission="app.admin.rule.delete">删除</button>
        </script>

        <script src="<?=__res('component/layui/layui.js')?>"></script>
        <script src="<?=__res('component/pear/pear.js')?>"></script>
        <script src="<?=__res('admin/js/permission.js')?>"></script>
        <script src="<?=__res('admin/js/common.js')?>"></script>

        <script>

            // 相关常量
            const PRIMARY_KEY = "id";
            const SELECT_API = "<?=__url('rule/select?limit=5000')?>";
            const DELETE_API = "<?=__url('rule/delete')?>";
            const UPDATE_API = "<?=__url('rule/update')?>";
            const INSERT_URL = "<?=__url('rule/insert')?>";
            const UPDATE_URL = "<?=__url('rule/update')?>";
            const SELECT_TREE_API = "<?=__url('rule/select?format=tree&type=0,1')?>";
            // 表格渲染
            layui.use(["table", "treetable", "form", "common", "popup", "util"], function() {
                let table = layui.table;
                let form = layui.form;
                let $ = layui.$;
                let common = layui.common;
                let treeTable = layui.treetable;
                let util = layui.util;

                // 表格头部列数据
                let cols = [
                    {
                        type: "checkbox"
                    },{
                        title: "标题",
                        field: "title",
                    },{
                        title: "图标",
                        field: "icon",
                        templet: function (d) {
                            return '<i class="layui-icon ' + util.escape(d["icon"]) + '"></i>';
                        }
                    },{
                        title: "主键",
                        field: "id",
                        hide: true,
                    },{
                        title: "key",
                        field: "key",
                    },{
                        title: "上级菜单",
                        field: "pid",
                        hide: true,
                        templet: function (d) {
                            let field = "pid";
                            if (typeof d[field] == "undefined") return "";
                            let items = [];
                            layui.each((d[field] + "").split(","), function (k , v) {
                                items.push(apiResults[field][v] || v);
                            });
                            return util.escape(items.join(","));
                        }
                    },{
                        title: "创建时间",
                        field: "created_at",
                        hide: true,
                    },{
                        title: "更新时间",
                        field: "updated_at",
                        hide: true,
                    },{
                        title: "url",
                        field: "href",
                    },{
                        title: "类型",
                        field: "type",
                        width: 80,
                        templet: function (d) {
                            let field = "type";
                            let value = apiResults["type"][d["type"]] || d["type"];
                            let css = {"目录":"layui-bg-blue", "菜单": "layui-bg-green", "权限": "layui-bg-orange"}[value];
                            return '<span class="layui-badge '+css+'">'+util.escape(value)+'</span>';
                        }
                    },{
                        title: "排序",
                        field: "weight",
                        width: 80,
                    },{
                        title: "操作",
                        toolbar: "#table-bar",
                        align: "center",
                        fixed: "right",
                        width: 130,
                    }
                ];

                // 渲染表格
                function render()
                {
                    treeTable.render({
                        elem: "#data-table",
                        url: SELECT_API,
                        treeColIndex: 1,
                        treeIdName: "id",
                        treePidName: "pid",
                        treeDefaultClose: true,
                        cols: [cols],
                        skin: "line",
                        size: "lg",
                        toolbar: "#table-toolbar",
                        defaultToolbar: [{
                            title: "刷新",
                            layEvent: "refresh",
                            icon: "layui-icon-refresh",
                        }, "filter", "print", "exports"]
                    });
                }

                // 获取下拉菜单及树形组件数据
                let apis = [];
                let apiResults = {};
                apiResults["pid"] = [];
                apis.push(["pid", SELECT_TREE_API]);
                apiResults["type"] = ["目录","菜单","权限"];
                let count = apis.length;
                layui.each(apis, function (k, item) {
                    let [field, url] = item;
                    $.ajax({
                        url: url,
                        dateType: "json",
                        success: function (res) {
                            if (res.code) {
                                return layui.popup.failure(res.msg);
                            }
                            function travel(items) {
                                for (let k in items) {
                                    let item = items[k];
                                    apiResults[field][item.value] = item.name;
                                    if (item.children) {
                                        travel(item.children);
                                    }
                                }
                            }
                            travel(res.data);
                        },
                        complete: function () {
                            if (--count === 0) {
                                render();
                            }
                        }
                    });
                });
                if (!count) {
                    render();
                }

                // 删除或编辑行事件
                table.on("tool(data-table)", function(obj) {
                    if (obj.event === "remove") {
                        remove(obj);
                    } else if (obj.event === "edit") {
                        edit(obj);
                    }
                });

                // 添加 批量删除 刷新事件
                table.on("toolbar(data-table)", function(obj) {
                    if (obj.event === "add") {
                        add();
                    } else if (obj.event === "refresh") {
                        refreshTable();
                    } else if (obj.event === "batchRemove") {
                        batchRemove(obj);
                    }
                });

                // 添加行
                let add = function() {
                    layer.open({
                        type: 2,
                        title: "新增",
                        shade: 0.1,
                        area: [common.isModile()?"100%":"520px", common.isModile()?"100%":"520px"],
                        content: INSERT_URL
                    });
                }

                // 编辑行
                let edit = function(obj) {
                    let value = obj.data[PRIMARY_KEY];
                    layer.open({
                        type: 2,
                        title: "修改",
                        shade: 0.1,
                        area: [common.isModile()?"100%":"520px", common.isModile()?"100%":"520px"],
                        content: UPDATE_URL + "?" + PRIMARY_KEY + "=" + value
                    });
                }

                // 删除行
                let remove = function(obj) {
                    return doRemove(obj.data[PRIMARY_KEY], obj);
                }

                // 删除多行
                let batchRemove = function(obj) {
                    let checkIds = common.checkField(obj, PRIMARY_KEY);
                    if (checkIds === "") {
                        layui.popup.warning("未选中数据");
                        return false;
                    }
                    doRemove(checkIds.split(","));
                }

                // 执行删除
                let doRemove = function (ids, obj) {
                    let data = {};
                    data[PRIMARY_KEY] = ids;
                    layer.confirm("确定删除?", {
                        icon: 3,
                        title: "提示"
                    }, function(index) {
                        layer.close(index);
                        let loading = layer.load();
                        $.ajax({
                            url: DELETE_API,
                            data: data,
                            dataType: "json",
                            type: "post",
                            success: function(res) {
                                layer.close(loading);
                                if (res.code) {
                                    return layui.popup.failure(res.msg);
                                }
                                return layui.popup.success("操作成功", function () {
                                    return obj ? obj.del() : refreshTable();
                                });
                            }
                        })
                    });
                }

                // 刷新表格
                window.refreshTable = function(param) {
                    treeTable.reload("#data-table");
                }
            })

        </script>
    </body>
</html>
