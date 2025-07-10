    <div id="js-main">
		<!-- 数据表格 -->
		<div class="layui-card">
			<div class="layui-card-body">
				<table id="data-table" lay-filter="data-table"></table>
			</div>
		</div>
        <!-- 表格顶部工具栏 -->
        <template id="table-toolbar">
            <button class="js-open-layer pear-btn pear-btn-md" permission="app.admin.rule.insert" href="insert?inframe=true" alt="新增"><i class="layui-icon layui-icon-add-1"></i>新增</button>
            <button class="js-batchremove pear-btn pear-btn-danger pear-btn-md" lay-event="batchRemove" permission="app.admin.rule.delete" href="delete?id={id}" ><i class="layui-icon layui-icon-delete"></i>删除</button>
        </template>
        
		<!-- 表格行工具栏 -->
        <template id="template-icon">
            <i class="layui-icon {{d.icon}}"></i>
        </template>
        <template id="template-type">
        {{# if(d.type==0){ }}
            <span class="layui-badge layui-bg-blue">目录</span>
        {{# }else if(d.type==1){ }}
            <span class="layui-badge layui-bg-green">菜单</span>
        {{# }else if(d.type==2){ }}
            <span class="layui-badge layui-bg-orange">权限</span>
        {{# } }}
        </template>
        <template id="table-bar">
            <button class="js-open-layer pear-btn pear-btn-xs tool-btn" permission="app.admin.rule.update" href="update?inframe=true&id={{d.id}}" alt="修改">编辑</button>
            <button class="js-delete pear-btn pear-btn-xs tool-btn" permission="app.admin.rule.delete" href="delete?id={{d.id}}">删除</button>
        </template>

<script src="<?=__res('admin/js/index.js')?>"></script>
<script>
<?php // 这段js 存放 动态数据 ?>
var data_permission = "<?=__url('rule/permission')?>";
// 相关常量
const PRIMARY_KEY = "id";
const SELECT_API = "<?=__url('rule/select?limit=5000')?>";
const DELETE_API = "<?=__url('rule/delete')?>";
const UPDATE_API = "<?=__url('rule/update')?>";
const INSERT_URL = "<?=__url('rule/insert')?>";
const UPDATE_URL = "<?=__url('rule/update')?>";
const SELECT_TREE_API = "<?=__url('rule/select?format=tree&type=0,1')?>";
</script>
<script>

// 表格渲染
layui.use(["table", "treetable", "form", "popup", "util"], function() {
    togglePermission(data_permission);
    toggleSearchFormShow();

    let table = layui.table;
    let form = layui.form;
    let $ = layui.$;
    let treeTable = layui.treetable;



    // 表格头部列数据
    let cols = [
        {type: "checkbox"},
        {title: "标题",field: "title"},
        {title: "图标",field: "icon",templet:'#template-icon'},
        {title: "主键",field: "id",hide: true},
        {title: "key",field: "key"},
        /*{title: "上级菜单",field: "pid",hide: true,templet: tmpl_parent_menu},*/ //这个可以读取上级 pid 名称
        {title: "创建时间",field: "created_at",hide: true},
        {title: "更新时间",field: "updated_at",hide: true},
        {title: "url",field: "href"},
        {title: "类型",field: "type",width: 80,templet:'#template-type'},
        {title: "排序",field: "weight",width: 80},
        {title: "操作",toolbar: "#table-bar",align: "center",fixed: "right",width: 130}
    ];
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
    enable_index_page_all(table);
    
});
		</script>
	</div>
