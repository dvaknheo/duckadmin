
<div class="layui-card">
  <div class="layui-card-body">
    <form class="layui-form" action="">
      <div class="layui-form-item">
        <div class="layui-form-item layui-inline">
          <label class="layui-form-label">账号</label>
          <div class="layui-input-inline">
            <input type="text" name="username" placeholder="" class="layui-input">
          </div>
        </div>
        <div class="layui-form-item layui-inline">
          <button class="pear-btn pear-btn-md pear-btn-primary" lay-submit lay-filter="query">
            <i class="layui-icon layui-icon-search"></i>
            查询
          </button>
          <button type="reset" class="pear-btn pear-btn-md">
            <i class="layui-icon layui-icon-refresh"></i>
            重置
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

<div class="layui-card">
<div class="layui-card-body"><div class="layui-table-tool-temp"> <button class="pear-btn pear-btn-primary pear-btn-md"> <i class="layui-icon layui-icon-add-1"></i> <a href="/admin/add">新增</a> </button> <button class="pear-btn pear-btn-danger pear-btn-md" lay-event="batchRemove"> <i class="layui-icon layui-icon-delete"></i> 删除 </button> <button class="pear-btn pear-btn-md" lay-event="recycle"> <i class="layui-icon layui-icon-delete"></i> 回收站 </button> </div><div class="layui-table-tool-self"><div class="layui-inline" title="刷新" lay-event="refresh"><i class="layui-icon layui-icon-refresh"></i></div><div class="layui-inline" title="筛选列" lay-event="LAYTABLE_COLS"><i class="layui-icon layui-icon-cols"></i></div><div class="layui-inline" title="打印" lay-event="LAYTABLE_PRINT"><i class="layui-icon layui-icon-print"></i></div><div class="layui-inline" title="导出" lay-event="LAYTABLE_EXPORT"><i class="layui-icon layui-icon-export"></i></div></div></div>
</div>

<div class="layui-card">
  <div class="layui-card-body">
    <table id="dataTable" lay-filter="dataTable"></table>
  </div>
</div>


<script type="text/html" id="status">
  <input type="checkbox" name="status" value="{{d.id}}" lay-skin="switch" lay-text="启用|禁用" lay-filter="status" {{# if(d.status==1){ }} checked {{# } }}>
</script>

<script>
var g_data=<?=json_encode($data);?>
</script>
<script>
layui.use(['table', 'form', 'jquery'], function() {
  let table = layui.table;
  let form = layui.form;
  let $ = layui.jquery;

  let MODULE_PATH = "{$route_path}/admin.admin/"; //  这里要改
  let cols = [[{
              type: 'checkbox'
          },
          {
              field: 'id',
              title: 'ID', 
              sort: true, 
              align: 'center',
              unresize: true
          },{
              title: '账号',
              field: 'username',
              align: 'center',
              unresize: true,
          },
          {
              title: '昵称',
              field: 'nickname',
              align: 'center',
              unresize: true,
          },
          {
              title: '状态',
              field: 'enable',
              align: 'center',
              unresize: true,
              templet: '#status'
          },
          {
              title: '操作',
              toolbar: '#options',
              align: 'center',
              unresize: true,
              width: 200
          }
      ]]

  table.render({
    elem: '#dataTable',
    data: g_data,
    
    page: true,
    cols: cols,
    cellMinWidth: 100,
    skin: 'line'
  });

  table.on('tool(dataTable)', function(obj) {
    if (obj.event === 'remove') {
      window.remove(obj);
    } else if (obj.event === 'edit') {
      window.edit(obj);
    } else if (obj.event === 'role') {
        window.role(obj);
    } else if (obj.event === 'permission') {
        window.permission(obj);
    }
  });

  table.on('toolbar(dataTable)', function(obj) {
    if (obj.event === 'add') {
      window.add();
    } else if (obj.event === 'refresh') {
      window.refresh();
    } else if (obj.event === 'batchRemove') {
        window.batchRemove(obj);
    } else if (obj.event === 'recycle') {
        window.recycle(obj);
    }
  });

  form.on('submit(query)', function(data) {
      table.reload('dataTable', {
          where: data.field,
          page:{curr: 1}
      })
      return false;
  });

  form.on('switch(status)', function(data) {
      var status = data.elem.checked?1:2;
      var id = this.value;
      var load = layer.load();
      $.post('status',{status:status,id:id},function (res) {
          layer.close(load);
          //判断有没有权限
          if(res && res.code==999){
              layer.msg(res.msg, {
                  icon: 5,
                  time: 2000, 
              })
              return false;
          }else if (res.code==200){
              layer.msg(res.msg,{icon:1,time:1500})
          } else {
              layer.msg(res.msg,{icon:2,time:1500},function () {
                  $(data.elem).prop('checked',!$(data.elem).prop('checked'));
                  form.render()
              })
          }
      })
  });
   //弹出窗设置 自己设置弹出百分比
  function screen() {
      if (typeof width !== 'number' || width === 0) {
      width = $(window).width() * 0.8;
      }
      if (typeof height !== 'number' || height === 0) {
      height = $(window).height() - 20;
      }
      return [width + 'px', height + 'px'];
  }

    window.edit = function(obj) {
      layer.open({
                    type: 2,
                    maxmin: true,
                    title: '修改管理员',
                    shade: 0.1,
                    area: screen(),
        content: MODULE_PATH + 'edit/id/'+obj.data['id']
      });
    }

    window.role  = function(obj) {
        layer.open({
            type: 2,
            maxmin: true,
            title: '分配角色',
            shade: 0.1,
            area: screen(),
            content: MODULE_PATH + 'role/id/'+obj.data['id']
        });
    }

    window.permission = function(obj) {
        layer.open({
            type: 2,
            maxmin: true,
            title: '分配直接权限',
            shade: 0.1,
            area: screen(),
            content: MODULE_PATH + 'permission/id/'+obj.data['id']
        });
    }

    window.recycle = function() {
        layer.open({
            type: 2,
            maxmin: true,
            title: '回收站',
            shade: 0.1,
            area: screen(),
            content: MODULE_PATH + 'recycle',
            cancel: function () {
                table.reload('dataTable');
            }
        });
    }

    window.remove = function(obj) {
        layer.confirm('确定要删除该管理', {
            icon: 3,
            title: '提示'
        }, function(index) {
            layer.close(index);
            let loading = layer.load();
            $.ajax({
                url:MODULE_PATH + 'remove',
                data:{id:obj.data['id']},
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
                            obj.del();
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
        layer.confirm('确定要删除这些管理员', {
            icon: 3,
            title: '提示'
        }, function(index) {
            layer.close(index);
            let loading = layer.load();
            $.ajax({
                url:MODULE_PATH + 'batchRemove',
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

    window.refresh = function(param) {
      table.reload('dataTable');
    }
  })
</script>