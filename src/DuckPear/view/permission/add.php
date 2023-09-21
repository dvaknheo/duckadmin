<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/static/component/pear/css/pear.css" />
    <style>
        .pear-container{background-color:white;}
        body{margin: 10px;}
    </style>
</head>
<body>
    <form class="layui-form" action="">
        <div class="layui-form-item">
            <label for="" class="layui-form-label">父级</label>
            <div class="layui-input-block">
                <select name="pid" lay-verify="requried">
                    <option value="0">顶级</option>
                    {foreach $permissions as $k1=>$p1}
                        <option value="{$p1.id}">{$p1.title}</option>
                        {if isset($p1['children']) && !empty($p1['children']) }
                            {foreach $p1['children'] as $k2=>$p2}
                                <option value="{$p2.id}" >&nbsp;&nbsp;&nbsp;┗━━{$p2.title}</option>
                            {/foreach}
                        {/if}
                    {/foreach}
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label for="" class="layui-form-label">权限名称</label>
            <div class="layui-input-block">
                <input type="text" maxlength="16" name="title"  lay-verify="required" placeholder="请输入权限名称" class="layui-input" >
            </div>
        </div>
        <div class="layui-form-item">
            <label for="" class="layui-form-label">地址</label>
            <div class="layui-input-block">
                <input type="text" name="href" placeholder="请输入地址" class="layui-input" >
            </div>
        </div>
        <div class="layui-form-item">
            <label for="" class="layui-form-label">图标</label>
            <div class="layui-input-block">
                <div class="layui-input-inline" style="width: unset">
                    <input type="text" id="iconPicker2" name="icon" value="" lay-filter="iconPicker2" class="hide">
                </div>
                <div class="layui-input-inline" style="width: unset">
                    <span class="pear-btn" id="clear">清空</span>
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">权限类型：</label>
            <div class="layui-input-block">
              <input type="radio" name="type" value="0" title="目录" checked>
              <input type="radio" name="type" value="1" title="菜单">
            </div>
        </div>
        <div class="layui-form-item">
            <label for="" class="layui-form-label">排序</label>
            <div class="layui-input-block">
                <input type="number" name="sort" value="10" lay-verify="required" placeholder="排序权重" class="layui-input" >
            </div>
        </div>
        <div class="bottom">
            <div class="button-container">
                <button type="submit" class="layui-btn layui-btn-normal layui-btn-sm" lay-submit="" lay-filter="save">
                    <i class="layui-icon layui-icon-ok"></i>
                    提交
                </button>
                <button type="reset" class="layui-btn layui-btn-primary layui-btn-sm">
                    <i class="layui-icon layui-icon-refresh"></i>
                    重置
                </button>
            </div>
    </form>
    <script src="/static/component/layui/layui.js"></script>
    <script src="/static/component/pear/pear.js"></script>
    <script>
        layui.use(['form','element','iconPicker','jquery'], function() {
            var form = layui.form;
            var element = layui.element;
            var iconPicker= layui.iconPicker;
            var $ = layui.jquery;
                iconPicker.render({
                    elem: '#iconPicker2',
                    type: 'fontClass',
                    search: true,
                    // 是否开启分页
                    page: true,
                    limit: 12,
                    click: function (data) {
                    },
                    success: function(d) {
                    }
                });
                $('#clear').click(function() {
                    $('#iconPicker2').attr("value","");
                    $('.layui-iconpicker-icon').children("i").attr("class","layui-icon layui-icon-circle-dot");
                });

                form.on('submit(save)', function(data){
                    data.field.icon = 'layui-icon '+ data.field.icon;
                    $.ajax({
                        data:JSON.stringify(data.field),
                        dataType:'json',
                        contentType:'application/json',
                        type:'post',
                        success:function(res){
                            //判断有没有权限
                            if(res && res.code==999){
                                layer.msg(res.msg, {
                                    icon: 5,
                                    time: 2000, 
                                })
                                return false;
                            }else if(res.code==200){
                                layer.msg(res.msg,{icon:1,time:1000},function(){
                                    parent.layer.close(parent.layer.getFrameIndex(window.name));//关闭当前页
                                    window.parent.location.reload();
                                });
                            }else{
                                layer.msg(res.msg,{icon:2,time:1000});
                            }
                        }
                    })
                    return false;
                });
        });
</script>
</body>
</html>