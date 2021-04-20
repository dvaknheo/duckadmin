
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
        <link rel="stylesheet" href="/static/component/pear/css/pear.css" />
	</head>
	<body class="pear-container">
		<div class="layui-card">
			<div class="layui-card-body">
				<table id="dataTable" lay-filter="dataTable"></table>
			</div>
		</div>

		<script type="text/html" id="toolbar">
			<button class="pear-btn pear-btn-primary pear-btn-md" lay-event="batchRecovery">
		        <i class="layui-icon layui-icon-refresh"></i>
		        恢复数据
			</button>
		    <button class="pear-btn pear-btn-danger pear-btn-md" lay-event="batchRemove">
		        <i class="layui-icon layui-icon-delete"></i>
		        彻底删除
		    </button>
		</script>

		<script src="/static/component/layui/layui.js"></script>
        <script src="/static/component/pear/pear.js"></script>
        <script>
            layui.use(['table', 'form', 'jquery'], function() {
                let table = layui.table;
                let form = layui.form;
                let $ = layui.jquery;

                let MODULE_PATH = "{$route_path}/admin.role/";

                let cols = [
                    [{
                            type: 'checkbox'
                        },{
                            field: 'id',
                            title: 'ID', 
                            sort: true, 
                            align: 'center',
                            unresize: true,
                            width: 80
                        },{
                            field: 'name',
                            title: '角色名称',
                            unresize: true,
                            align: 'center'
                        },{
                            field: 'desc',
                            title: '描述',
                            unresize: true,
                            align: 'center',
                        },{
                            field: 'create_time',
                            title: '创建时间',
                            unresize: true,
                            align: 'center'
                        },{
                            field: 'update_time',
                            title: '更新时间',
                            unresize: true,
                            align: 'center'
                        },{
                            field: 'delete_time',
                            title: '删除时间',
                            unresize: true,
                            align: 'center'
                        }
                    ]
                ]

                table.render({
                    elem: '#dataTable',
                    url: MODULE_PATH + 'recycle',
                    page: true,
                    cols: cols,
                    cellMinWidth: 100,
                    skin: 'line',
                    toolbar: '#toolbar',
                    defaultToolbar: [{
                        layEvent: 'refresh',
                        icon: 'layui-icon-refresh',
                    }, 'filter', 'print', 'exports']
                });

                table.on('toolbar(dataTable)', function(obj) {
                    if (obj.event === 'batchRemove') {
                        window.batchRemove(obj);
                    }else if (obj.event === 'batchRecovery') {
                        window.batchRecovery(obj);
                    }else if (obj.event === 'refresh') {
                        window.refresh();
                    } 
                });

                window.batchRecovery = function(obj) {
                    let data = table.checkStatus(obj.config.id).data;
                    if (data.length === 0) {
                        layer.msg("未选中数据", {
                            icon: 3,
                            time: 1000
                        });
                        return false;
                    }
                    var ids = []
                    var hasCheck = table.checkStatus('dataTable')
                    var hasCheckData = hasCheck.data
                    if (hasCheckData.length > 0) {
                        $.each(hasCheckData, function (index, element) {
                            ids.push(element.id)
                        })
                    }
                    layer.confirm('确定要恢复这些角色', {
                        icon: 3,
                        title: '提示'
                    }, function(index) {
                        layer.close(index);
                        let loading = layer.load();
                        $.ajax({
                            url: MODULE_PATH + 'recycle',
                            data:{ids:ids,type:true},
                            dataType: 'json',
                            type: 'POST',
                            success: function(res) {
                                layer.close(loading);
                                //判断有没有权限
                                if(res && res.code==999){
                                    layer.msg(res.msg, {
                                        icon: 5,
                                        time: 2000, 
                                    })
                                    return false;
                                }else if(res.code==200) {
                                    layer.msg(res.msg, {
                                        icon: 1,
                                        time: 1000
                                    }, function() {
                                        table.reload('dataTable');
                                    });
                                } else {
                                    layer.msg(res.msg, {
                                        icon: 2,
                                        time: 1000
                                    });
                                }
                            }
                        })
                    });
                }

                window.batchRemove = function(obj) {
                    let data = table.checkStatus(obj.config.id).data;
                    if (data.length === 0) {
                        layer.msg("未选中数据", {
                            icon: 3,
                            time: 1000
                        });
                        return false;
                    }
                    var ids = []
                    var hasCheck = table.checkStatus('dataTable')
                    var hasCheckData = hasCheck.data
                    if (hasCheckData.length > 0) {
                        $.each(hasCheckData, function (index, element) {
                            ids.push(element.id)
                        })
                    }
                    layer.confirm('确定要删除这些角色', {
                        icon: 3,
                        title: '提示'
                    }, function(index) {
                        layer.close(index);
                        let loading = layer.load();
                        $.ajax({
                            url: MODULE_PATH + 'recycle',
                            data:{ids:ids},
                            dataType: 'json',
                            type: 'POST',
                            success: function(res) {
                                layer.close(loading);
                                //判断有没有权限
                                if(res && res.code==999){
                                    layer.msg(res.msg, {
                                        icon: 5,
                                        time: 2000, 
                                    })
                                    return false;
                                }else if (res.code==200) {
                                    layer.msg(res.msg, {
                                        icon: 1,
                                        time: 1000
                                    }, function() {
                                        table.reload('dataTable');
                                    });
                                } else {
                                    layer.msg(res.msg, {
                                        icon: 2,
                                        time: 1000
                                    });
                                }
                            }
                        })
                    });
                }

                window.refresh = function() {
                    table.reload('dataTable');
                }
            })
        </script>
	</body>
</html>
