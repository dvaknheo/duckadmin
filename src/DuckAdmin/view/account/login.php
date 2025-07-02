<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <title>登录</title>
        <!-- 样 式 文 件 -->
        <link rel="stylesheet" href="<?=__res('component/pear/css/pear.css')?>" />
<style>
.layui-form {
	width: 320px !important;
	margin: auto !important;
	margin-top: 160px !important;
}

.layui-form button {
	width: 100% !important;
	height: 44px !important;
	line-height: 44px !important;
	font-size: 16px !important;
	font-weight: 550 !important;
}

.layui-form-checked[lay-skin=primary] i {
	color: #fff !important;
}

.layui-tab-content {
	margin-top: 15px !important;
	padding-left: 0px !important;
	padding-right: 0px !important;
}

.layui-form-item {
	margin-top: 20px !important;
}

.layui-input {
	height: 44px !important;
	line-height: 44px !important;
	padding-left: 15px !important;
	border-radius: 3px !important;
}

.layui-form-danger:focus{
	box-shadow: 0px 0px 2px 1px #f56c6c !important;
}

.logo {
	width: 60px !important;
	margin-top: 10px !important;
	margin-bottom: 10px !important;
	margin-left: 20px !important;
}

.title {
	font-size: 30px !important;
	font-weight: 550 !important;
	margin-left: 20px !important;
	display: inline-block !important;
	height: 60px !important;
	line-height: 60px !important;
	margin-top: 10px !important;
	position: absolute !important;
}

.desc {
	width: 100% !important;
	text-align: center !important;
	color: gray !important;
	height: 60px !important;
	line-height: 60px !important;
}

body {
	background-repeat:no-repeat;
	background-color: whitesmoke;
	background-size: 100%;
	height: 100%;
 }

.code {
	float: left;
	margin-right: 13px;
	margin: 0px !important;
	border: #e6e6e6 1px solid;
	display: inline-block!important;
}

.codeImage {
	float: right;
	height: 42px;
	border: #e6e6e6 1px solid;
}

@media (max-width:768px){
	body{
		background-position:center;
	}
}
</style>
    </head>
    <!-- 代 码 结 构 -->
    <body background="<?=__res('admin/images/background.svg')?>" style="background-size: cover;">
        <form class="layui-form" method="post" action="<?=__url('account/login')?>">
            <div class="layui-form-item">
                <img class="logo" src="<?=__res('admin/images/logo.png')?>" />
                <div class="title pear-text">admin</div>
            </div>
            <div class="layui-form-item">
                <input lay-verify="required" hover class="layui-input" type="text" name="username" value="" placeholder="用户名" />
            </div>
            <div class="layui-form-item">
                <input lay-verify="required" hover class="layui-input" type="password" name="password" value="" placeholder="密码" />
            </div>
            <div class="layui-form-item">
                <input hover  lay-verify="required" class="code layui-input layui-input-inline" name="captcha" placeholder="验证码" />
                <img class="codeImage" src-ref="<?=__url('account/captcha?type=login&v=')?>" width="120px"/>
            </div>
            <div class="layui-form-item">
                <button type="submit" class="pear-btn pear-btn-primary login" lay-submit lay-filter="login">
                    登 入
                </button>
            </div>
        </form>
        <!-- 资 源 引 入 -->
        <script src="<?=__res('component/layui/layui.js')?>"></script>
        <script src="<?=__res('component/pear/pear.js')?>"></script>
        <script src="<?=__res('admin/js/ajax_post.js')?>"></script>
        <script>
var url_back=<?=json_encode($url_back)?>;
</script>
<script>
layui.use(['form', 'popup', 'layer'], function() {
    layer = layui.layer;
    function switchCaptcha() {
        var  url_captcha = document.querySelector('.codeImage').getAttribute('src-ref');
        document.querySelector('.codeImage').setAttribute("src", url_captcha + new Date().getTime());
    }
    document.querySelector('.codeImage').addEventListener('click', function () {
        switchCaptcha();
    });
    switchCaptcha();
    // 登 录 提 交
    layui.form.on('submit(login)', function (data) {
        ajax_post(this.closest('form'), function (res) {
            layer.closeAll('loading');
            if (res.code) {
                layui.popup.failure(res.msg, function () {
                    switchCaptcha();
                });
                return;
            }
            layui.popup.success('登录成功', function () {
                if(url_back){
                    location.href = url_back;
                    return;
                }
                location.reload();
            })
        });
        return false;
    });

})
</script>
    </body>
</html>