    <div id="js-main">
    
        <!-- 顶部查询表单 -->
        
        
        <!-- 数据表格 -->
        <div class="layui-card">
            <div class="layui-card-body">
                <table id="data-table" lay-filter="data-table"></table>
            </div>
        </div>

        <!-- 表格顶部工具栏 -->
        <template id="table-toolbar">
            <button class="js-open-layer pear-btn pear-btn-md" permission="app.admin.role.insert" href="insert?inframe=true" alt="新增"><i class="layui-icon layui-icon-add-1"></i>新增</button>
            <button class="js-batchremove pear-btn pear-btn-danger pear-btn-md" lay-event="batchRemove" permission="app.admin.role.delete" href="delete?id={id}" ><i class="layui-icon layui-icon-delete"></i>删除</button>
        </template>


        <!-- 表格行工具栏 --><!-- 根 role 不能被删除 -->
        <script type="text/html" id="table-bar">
            {{# if(d.id!==1&&d.pid&&!d.isRoot){ }}
            <button class="js-open-layer pear-btn pear-btn-xs tool-btn" permission="app.admin.role.update" href="update?inframe=true&id={{d.id}}" alt="修改">编辑</button>
            <button class="js-delete pear-btn pear-btn-xs tool-btn" permission="app.admin.role.delete" href="delete?id={{d.id}}">删除</button>
            {{# } }}
        </script>
<script src="<?=__res('admin/js/index.js')?>"></script>
<script>
<?php // 这段js 存放 动态数据 ?>
var data_permission = "<?=__url('rule/permission')?>";
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
        return "???";
        let field = "rules";
        if (typeof d[field] == "undefined") return "";
        let items = [];
        layui.each((d[field] + "").split(","), function (k , v) {
            items.push(apiResults[field][v] || v);
        });
        return util.escape(items.join(","));
    }
    var tmpl_pids = function (d) {
        return "???";
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
        {title: "权限",field: "rules",templet: tmpl_rules,hide: true,},  // 这应该由服务端获取
        {title: "创建时间",field: "created_at",},
        {title: "更新时间",field: "updated_at",},
        {title: "父级",field: "pid",templet: tmpl_pids,hide: true,}, // 这应该由服务端获取
        {title: "操作",toolbar: "#table-bar",align: "center",fixed: "right",width: 120,}
    ];

    treeTable.render({
        elem: "#data-table",
        url: "select",
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
    enable_index_page_all(table);
})

        </script>
    </div>
