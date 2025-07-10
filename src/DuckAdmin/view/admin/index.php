    <div id="js-main">
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
        <template id="table-toolbar">
            <button class="js-open-layer pear-btn pear-btn-md" permission="app.admin.admin.insert" href="insert?inframe=true" alt="新增"><i class="layui-icon layui-icon-add-1"></i>新增</button>
            <button class="js-batchremove pear-btn pear-btn-danger pear-btn-md" lay-event="batchRemove" permission="app.admin.admin.delete" href="delete?id={id}" ><i class="layui-icon layui-icon-delete"></i>删除</button>
        </template>

        <!-- 表格行工具栏 -->
        <!-- 这里不能用template 标签，因为不满足html -->
        <script type="text/html" id="template-status">
            {{# if(g_admin_id !== d.id){ }}
                {{# if(d.status==1){ }}
                    <input type="checkbox" value="{{d.id}}" lay-filter="status" lay-skin="switch" lay-text="" checked="checked">
                {{# }else{ }}
                    <input type="checkbox" value="{{d.id}}" lay-filter="status" lay-skin="switch" lay-text="">
                {{# } }}
            {{# } }}
        </script>
        <template id="table-bar">
            <button class="js-open-layer pear-btn pear-btn-xs tool-btn" permission="app.admin.admin.update" href="update?inframe=true&id={{d.id}}" alt="修改">编辑</button>
            <button class="js-delete pear-btn pear-btn-xs tool-btn" permission="app.admin.admin.delete" href="delete?id={{d.id}}">删除</button>
        </template>
<script src="<?=__res('admin/js/index.js')?>"></script>
<script>
<?php // 这段js 存放 动态数据 ?>
var data_permission = "<?=__url('rule/permission')?>";
const UPDATE_API = "<?=__url('admin/update')?>"; // 这个只是改状态
var g_admin_id = <?=$current_admin_id?>;
</script>
<script>
layui.use(["table", "form",  "popup", "laydate"], function() {
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
    
    /////////////////////////////////////////////////////
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
        {title: "角色",field: "roles"}, // 这里直接从服务端获取 角色名称就够了。 1,2,3 =>'超管之类'
        {title: "禁用",field: "status",templet: "#template-status",width: 90,},
        {title: "操作",toolbar: "#table-bar",align: "center",fixed: "right",width: 130,}
    ];
    table.render({
        elem: "#data-table",
        url: "select",
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
    });
    enable_index_page_all(table);
    ///////////////////////////////
    //这里 toggle 按钮。
    form.on("switch(status)", function (data) {
        let load = layer.load();
        let postData = {
            id: this.value,
            status: data.elem.checked ? 1 : 0,
        };
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
})

        </script>
    </div>
