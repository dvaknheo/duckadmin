
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
        <div class="layui-card">
            <div class="layui-card-body">
                <form class="layui-form top-search-from">
                    
                    <div class="layui-form-item">
                        <label class="layui-form-label">用户名</label>
                        <div class="layui-input-block">
                            <input type="text" name="username" value="" class="layui-input">
                        </div>
                    </div>
                    
                    <div class="layui-form-item">
                        <label class="layui-form-label">昵称</label>
                        <div class="layui-input-block">
                            <input type="text" name="nickname" value="" class="layui-input">
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
                    
                    <div class="layui-form-item">
                        <label class="layui-form-label">创建时间</label>
                        <div class="layui-input-block">
                            <div class="layui-input-block" id="created_at">
                                <input type="text" autocomplete="off" name="created_at[]" id="created_at-date-start" class="layui-input inline-block" placeholder="开始时间">
                                -
                                <input type="text" autocomplete="off" name="created_at[]" id="created_at-date-end" class="layui-input inline-block" placeholder="结束时间">
                            </div>
                        </div>
                    </div>
                    
                    <div class="layui-form-item layui-inline">
                        <label class="layui-form-label"></label>
                        <button class="pear-btn pear-btn-md pear-btn-primary" lay-submit lay-filter="table-query">
                            <i class="layui-icon layui-icon-search"></i>查询
                        </button>
                        <button type="reset" class="pear-btn pear-btn-md" lay-submit lay-filter="table-reset">
                            <i class="layui-icon layui-icon-refresh"></i>重置
                        </button>
                    </div>
                    <div class="toggle-btn">
                        <a class="layui-hide">展开<i class="layui-icon layui-icon-down"></i></a>
                        <a class="layui-hide">收起<i class="layui-icon layui-icon-up"></i></a>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- 数据表格 -->
        <div class="layui-card">
            <div class="layui-card-body">
                <table id="data-table" lay-filter="data-table"></table>
            </div>
        </div>

        <!-- 表格顶部工具栏 -->
        <script type="text/html" id="table-toolbar">
            <button class="pear-btn pear-btn-primary pear-btn-md" lay-event="add" permission="app.admin.admin.insert">
                <i class="layui-icon layui-icon-add-1"></i>新增
            </button>
            <button class="pear-btn pear-btn-danger pear-btn-md" lay-event="batchRemove" permission="app.admin.admin.delete">
                <i class="layui-icon layui-icon-delete"></i>删除
            </button>
        </script>

        <!-- 表格行工具栏 -->
        <script type="text/html" id="table-bar">
            {{# if(d.show_toolbar){ }}
            <button class="pear-btn pear-btn-xs tool-btn" lay-event="edit" permission="app.admin.admin.update">编辑</button>
            <button class="pear-btn pear-btn-xs tool-btn" lay-event="remove" permission="app.admin.admin.delete">删除</button>
            {{# } }}
        </script>
        <script src="<?=__res('component/layui/layui.js')?>"></script>
        <script src="<?=__res('component/pear/pear.js')?>"></script>
        <script src="<?=__res('admin/js/common.js')?>"></script>
<script>
<?php // 这段js 存放 动态数据 ?>
var data_permission = "<?=__url('rule/permission')?>";
const PRIMARY_KEY = "id";
const INSERT_URL = "<?=__url('admin/insert')?>";
const SELECT_API = "<?=__url('admin/select')?>";
const UPDATE_API = "<?=__url('admin/update')?>";
const UPDATE_URL = "<?=__url('admin/update')?>"; 
const DELETE_API = "<?=__url('admin/delete')?>";
const URL_ROLE_SELECT ="<?=__url('role/select?format=select')?>";
var g_admin_id = 1;  //TODO 这里要改成当前用户
</script>
<script>
// 相关常量

layui.use(["table", "form",  "popup", "util","laydate"], function() {
    // 字段 创建时间 created_at
    layui.laydate.render({
        elem: "#created_at",
        range: ["#created_at-date-start", "#created_at-date-end"],
    });
    togglePermission(data_permission);
    toggleSearchFormShow();
    // 表格渲染
    let table = layui.table;
    let form = layui.form;
    let $ = layui.$;
    let common = layui.common;
    let util = layui.util;
    
    var tmpl_roles = function (d) {
        let field = "roles";
        if (typeof d[field] == "undefined") return "";
        let items = [];
        layui.each((d[field] + "").split(","), function (k , v) {
            items.push(apiResults[field][v] || v);
        });
        return util.escape(items.join(","));
    };
    var tmpl_status = function (d) {
        let field = "status";
        form.on("switch("+field+")", function (data) {
            let load = layer.load();
            let postData = {};
            postData[field] = data.elem.checked ? 1 : 0;
            postData[PRIMARY_KEY] = this.value;
            $.post(UPDATE_API, postData, function (res) {
                layer.close(load);
                if (res.code) {
                    return layui.popup.failure(res.msg, function () {
                        data.elem.checked = !data.elem.checked;
                        form.render();
                    });
                }
                return layui.popup.success("操作成功");
            })
        });
        let checked = d[field] === 1 ? "checked" : "";
        if (g_admin_id === d.id) return ''; //这里要改
        return '<input type="checkbox" value="'+util.escape(d[PRIMARY_KEY])+'" lay-filter="'+util.escape(field)+'" lay-skin="switch" lay-text="'+util.escape('')+'" '+checked+'/>';
    };
    // 表头参数
    let cols = [
        {type: "checkbox"},
        {title: "ID",field: "id",width: 100,sort: true,},
        {title: "用户名",field: "username",},
        {title: "昵称",field: "nickname",},
        {title: "密码",field: "password",hide: true,},
        {title: "邮箱",field: "email",hide: true,},
        {title: "手机",field: "mobile",hide: true,},
        {title: "创建时间",field: "created_at",hide: true,},
        {title: "更新时间",field: "updated_at",hide: true,},
        {title: "登录时间",field: "login_at",},
        {title: "角色",field: "roles",templet: tmpl_roles},
        {title: "禁用",field: "status",templet: tmpl_status,width: 90,},
        {title: "操作",toolbar: "#table-bar",align: "center",fixed: "right",width: 130,}
    ];
    
    /////////////////////////////////////////////////////
    // 渲染表格
    function render()
    {
        table.render({
            elem: "#data-table",
            url: SELECT_API,
            page: true,
            cols: [cols],
            skin: "line",
            size: "lg",
            toolbar: "#table-toolbar",
            autoSort: false,
            defaultToolbar: [{
                title: "刷新",
                layEvent: "refresh",
                icon: "layui-icon-refresh",
            }, "filter", "print", "exports"],
            done: function () {
                layer.photos({photos: 'div[lay-id="data-table"]', anim: 5});
            }
        });
    }
    
    // 获取表格中下拉或树形组件数据
    let apis = [];
    apis.push(["roles", URL_ROLE_SELECT]);
    let apiResults = {};
    apiResults["roles"] = [];
    let count = apis.length;
    layui.each(apis, function (k, item) {
        let [field, url] = item;
        fetch_data_and_run(url, function(data){
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
                if (--count === 0) {
                    render();
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
        table.reloadData("data-table", {
            scrollPos: "fixed"
        });
    }
})

        </script>
    </body>
</html>
