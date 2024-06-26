<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <title>登录</title>
        <!-- 样 式 文 件 -->
        <link rel="stylesheet" href="<?=__res('component/pear/css/pear.css')?>" />
        <link rel="stylesheet" href="<?=__res('admin/css/pages/login.css')?>" />
    </head>
    <!-- 代 码 结 构 -->
    <body background="<?=__res('admin/images/background.svg')?>" style="background-size: cover;">
        <form class="layui-form">
            <div class="layui-form-item">
                <img class="logo" src="<?=__res('admin/images/logo.png')?>" />
                <div class="title pear-text">duckadmin</div>
            </div>
            <div class="layui-form-item">
                <input lay-verify="required" hover class="layui-input" type="text" name="username" value="" placeholder="用户名" />
            </div>
            <div class="layui-form-item">
                <input lay-verify="required" hover class="layui-input" type="password" name="password" value="" placeholder="密码" />
            </div>
            <div class="layui-form-item">
                <input hover  lay-verify="required" class="code layui-input layui-input-inline" name="captcha" placeholder="验证码" />
                <img class="codeImage" width="120px"/>
            </div>
            <div class="layui-form-item">
                <button type="submit" class="pear-btn pear-btn-primary login" lay-submit lay-filter="login">
                    【登 入】
                </button>
            </div>
        </form>
        <script>
            var color = localStorage.getItem("theme-color-color");
            var second = localStorage.getItem("theme-color-second");
            if (!color || !second) {
                localStorage.setItem("theme-color-color", "#2d8cf0");
                localStorage.setItem("theme-color-second", "#ecf5ff");
            }
        </script>
        <!-- 资 源 引 入 -->
        <script src="<?=__res('component/layui/layui.js')?>"></script>
        <script src="<?=__res('component/pear/pear.js')?>"></script>
        <script>
         var url_back=<?=json_encode($url_back)?>;
       </script>
        <script>
            layui.use(['form', 'button', 'popup', 'layer', 'theme', 'admin'], function() {

                var $ = layui.$, layer = layui.layer, form = layui.form;
                function switchCaptcha() {
                    $('.codeImage').attr("src", "<?=__url('account/captcha?type=login&v=')?>" + new Date().getTime());
                }
                switchCaptcha();
                // 登 录 提 交
                form.on('submit(login)', function (data) {
                    layer.load();
                    $.ajax({
                        url: '<?=__url('account/login')?>',
                        type: "POST",
                        data: data.field,
                        success: function (res) {
                            layer.closeAll('loading');
                            if (!res.code) {
                                layui.popup.success('登录成功', function () {
                                    //location.reload();
                                    if(url_back){
                                        location.href = url_back;
                                    }else{
                                        location.reload();
                                    }
                                })
                            } else {
                                layui.popup.failure(res.msg, function () {
                                    switchCaptcha();
                                })
                            }
                        }
                    });
                    return false;
                });
                $('.codeImage').on('click', function () {
                    switchCaptcha();
                });
            })
        </script>
    </body>
</html>