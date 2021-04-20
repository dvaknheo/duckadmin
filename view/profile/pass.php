<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/static/component/pear/css/pear.css" />
</head>
<body>
    <form class="layui-form" method="post">
        <div class="mainBox">
            <div class="main-container">
                <div class="layui-form-item">
                    <label class="layui-form-label">密码</label>
                    <div class="layui-input-block">
                        <input type="password" name="password"  placeholder="请输入新密码" class="layui-input">
                    </div>
                </div>
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
    </div>
</form>
<script src="/static/component/layui/layui.js"></script>
<script src="/static/component/pear/pear.js"></script>
<script>
layui.use(['form','jquery'],function(){
    let form = layui.form;
    let $ = layui.jquery;
    form.on('submit(save)', function(data){
        $.ajax({
            data:JSON.stringify(data.field),
            dataType:'json',
            contentType:'application/json',
            type:'post',
            success:function(res){
                if (res.code==200) {
                    layer.msg(res.msg, {
                        icon: 1
                    });
                    setTimeout(function() {
                        window.parent.location = '{$route_path}/login/index';
                        var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
                        parent.layer.close(index);
                    }, 111)
                }else {
                    layer.msg(res.msg,{icon:2,time:1500})
                }
            }
        })
        return false;
    });
})
</script>
</body>
</html>