
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>浏览页面</title>
        <link rel="stylesheet" href="<?=__res('component/pear/css/pear.css')?>" />
        <link rel="stylesheet" href="<?=__res('admin/css/reset.css')?>" />
    </head>
    <body class="pear-container">
    
        <!-- 顶部查询表单 -->
        
        
        <!-- 数据表格 -->
        <div class="layui-card">
            <div class="layui-card-body">
                <table id="data-table" lay-filter="data-table"></table>
            </div>
        </div>

        <!-- 表格顶部工具栏 -->
        <script type="text/html" id="table-toolbar">
            <button class="pear-btn pear-btn-primary pear-btn-md" lay-event="add" permission="app.admin.role.insert">
                <i class="layui-icon layui-icon-add-1"></i>新增
            </button>
            <button class="pear-btn pear-btn-danger pear-btn-md" lay-event="batchRemove" permission="app.admin.role.delete">
                <i class="layui-icon layui-icon-delete"></i>删除
            </button>
        </script>

        <!-- 表格行工具栏 -->
        <script type="text/html" id="table-bar">
            {{# if(d.id!==1&&d.pid&&!d.isRoot){ }}
            <button class="pear-btn pear-btn-xs tool-btn" lay-event="edit" permission="app.admin.role.update">编辑</button>
            <button class="pear-btn pear-btn-xs tool-btn" lay-event="remove" permission="app.admin.role.delete">删除</button>
            {{# } }}
        </script>
        <script src="<?=__res('component/layui/layui.js')?>"></script>
        <script src="<?=__res('component/pear/pear.js')?>"></script>
        <script src="<?=__res('admin/js/common.js')?>"></script>
<script>
<?php // 这段js 存放 动态数据 ?>
var data_permission = "<?=__url('rule/permission')?>";
const PRIMARY_KEY = "id";
const SELECT_API = "<?=__url('role/select')?>";
const UPDATE_API = "<?=__url('role/update')?>";
const DELETE_API = "<?=__url('role/delete')?>";
const INSERT_URL = "<?=__url('role/insert')?>";
const UPDATE_URL = "<?=__url('role/update')?>";

const URL_RULE_LIST = "<?=__url('rule/get?type=0,1,2')?>";
const URL_ROLE_TREE ="<?=__url('role/select?format=tree')?>";

</script>
<script>
// 表格渲染
layui.use(["table", "treetable", "form", "popup", "util"], function() {
    togglePermission(data_permission);
    toggleSearchFormShow();
    
    let treeTable = layui.treetable;
    let table = layui.table;
    let form = layui.form;
    let $ = layui.$;
    let common = layui.common;
    let util = layui.util;
    var tmpl_rules = function (d) {
        let field = "rules";
        if (typeof d[field] == "undefined") return "";
        let items = [];
        layui.each((d[field] + "").split(","), function (k , v) {
            items.push(apiResults[field][v] || v);
        });
        return util.escape(items.join(","));
    }
    var tmpl_pids = function (d) {
        let field = "pid";
        if (typeof d[field] == "undefined") return "";
        let items = [];
        layui.each((d[field] + "").split(","), function (k , v) {
            items.push(apiResults[field][v] || v);
        });
        return util.escape(items.join(","));
    }
    // 表头参数
    let cols = [
        {type: "checkbox"},
        {title: "角色组",field: "name",},
        {title: "主键",field: "id",},
        {title: "权限",field: "rules",templet: tmpl_rules,hide: true,},
        {title: "创建时间",field: "created_at",},
        {title: "更新时间",field: "updated_at",},
        {title: "父级",field: "pid",templet: tmpl_pids,hide: true,},
        {title: "操作",toolbar: "#table-bar",align: "center",fixed: "right",width: 120,}
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
            treeDefaultClose: false,
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
    
    // 获取表格中下拉或树形组件数据
    let apis = [];
    apis.push(["rules", URL_RULE_LIST]);
    apis.push(["pid", URL_ROLE_TREE]);
    let apiResults = {};
    apiResults["rules"] = [];
    apiResults["pid"] = [];
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
                var data = res.data;
                function travel(items) {
                    for (let k in items) {
                        let item = items[k];
                        apiResults[field][item.value] = item.name;
                        if (item.children) {
                            travel(item.children);
                        }
                    }
                }
                travel(data);
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
    
    // 编辑或删除行事件
    table.on("tool(data-table)", function(obj) {
        if (obj.event === "remove") {
            remove(obj);
        } else if (obj.event === "edit") {
            edit(obj);
        }
    });

    // 表格顶部工具栏事件
    table.on("toolbar(data-table)", function(obj) {
        if (obj.event === "add") {
            add();
        } else if (obj.event === "refresh") {
            refreshTable();
        } else if (obj.event === "batchRemove") {
            batchRemove(obj);
        }
    });

    // 表格顶部搜索事件
    form.on("submit(table-query)", function(data) {
        table.reload("data-table", {
            where: data.field
        })
        return false;
    });
    
    // 表格顶部搜索重置事件
    form.on("submit(table-reset)", function(data) {
        table.reload("data-table", {
            where: []
        })
    });

    // 表格排序事件
    table.on("sort(data-table)", function(obj){
        table.reload("data-table", {
            initSort: obj,
            scrollPos: "fixed",
            where: {
                field: obj.field,
                order: obj.type
            }
        });
    });

    // 表格新增数据
    let add = function() {
        layer.open({
            type: 2,
            title: "新增",
            shade: 0.1,
            area: [common_isModile()?"100%":"500px", common_isModile()?"100%":"450px"],
            content: INSERT_URL
        });
    }

    // 表格编辑数据
    let edit = function(obj) {
        let value = obj.data[PRIMARY_KEY];
        layer.open({
            type: 2,
            title: "修改",
            shade: 0.1,
            area: [common_isModile()?"100%":"500px", common_isModile()?"100%":"450px"],
            content: UPDATE_URL + "?" + PRIMARY_KEY + "=" + value
        });
    }

    // 删除一行
    let remove = function(obj) {
        return doRemove(obj.data[PRIMARY_KEY]);
    }

    // 删除多行
    let batchRemove = function(obj) {
        let checkIds = common_checkField(obj, PRIMARY_KEY);
        if (checkIds === "") {
            layui.popup.warning("未选中数据");
            return false;
        }
        doRemove(checkIds.split(","));
    }

    // 执行删除
    let doRemove = function (ids) {
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
                    return layui.popup.success("操作成功", refreshTable);
                }
            })
        });
    }

    // 刷新表格数据
    window.refreshTable = function(param) {
        treeTable.reload("#data-table");
    }
})

        </script>
    </body>
</html>
