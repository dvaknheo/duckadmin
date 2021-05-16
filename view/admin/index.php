
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
    <div class="layui-card-body">
    <button class="pear-btn pear-btn-primary pear-btn-md"> <i class="layui-icon layui-icon-add-1"></i> <a href="<?=__url('admin/add')?>">新增</a> </button>
    </div>
</div>

<div class="layui-card">
  <div class="layui-card-body">
    <table  class="layui-table">
        <thead>
        <tr>
            <th>ID</th>
            <th>姓名</th>
            <th>账号</th>
            <th>职位</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
<?php
    foreach($data as $v){
?>
        <tr>
            <td><?=$v['id']?></td>
            <td><?=__h($v['nickname'])?></td>
            <td><?=__h($v['username'])?></td>
            <td><?=__h($roles[$v['role_id']])?></td>
            <td>
                <a href="<?=__url('admin/edit?id='.$v['id'])?>">编辑</a>
                <a href="#">删除</a>
            </td>
        </tr>
<?php
    }
?>
        </tbody>
    </table>
  </div>
</div>


<script type="text/html" id="status">
  <input type="checkbox" name="status" value="{{d.id}}" lay-skin="switch" lay-text="启用|禁用" lay-filter="status" {{# if(d.status==1){ }} checked {{# } }}>
</script>
