<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>DuckAdmin 后台系统</title>
		<link href="<?=duckadmin_res("layui/css/layui.css")?>" rel="stylesheet" />
	</head>
	<body>
    
<div class="layui-header" style ="background-color:#5FB878;padding:1em;">
    <div>
        <h1>DuckAdmin</h1><span> DuckPhp 的演示</span>
    </div>
</div>
  <div class="layui-container">
<!-- -->

    <div class="layui-row " style="border-left:1px solid;border-right:1px solid;padding:1em;">
		<div class="layui-col-md8" >
<!-- -->
<h1>欢迎使用 DuckAdmin</h1>

<hr />
<?php
if(defined('DUCKADMIN_DIRECT_MODE')){
?>
<blockquote class="layui-elem-quote">
你使用的是直接调用的模式
请注意 DuckAdmin 是 Composer Library 。而不是 Composer Project 。
简而言之，请勿在 DuckAdmin 作为你的桩代码来修改，而是在你的工程里引入。
</blockquote>

<?php
}
?>
<h2>安装</h2>
<pre>
DuckAdmin 是用来给 DuckPhp 项目做二次开发用的。
你应该在调整选项
<pre class="layui-code">
$options['ext'][\DuckAdmin\Api\DuckAdminPlugin::class] = [
    // 你要添加的选项
];
</pre>
TODO 安装要点：<br />

TODO 数据库前缀

<h2>覆盖这个View</h2>

最必须的魔改： 这个首页要替换
在你的工程添加，<code>view/DuckAdimin/index.php</code>

这个文件，重新设立你的入口页面。
<br>

<h2>使用 DuckAdmin 的服务</h2>
控制器助手类： 
<pre class="layui-code">
class MyController
{
    public function __contruct()
    {
        \DuckAdmin\Api\DuckAdminControllerApi::CheckPermission();
        // 这使得你的控制器必须有权限才能访问。
    }
}
</pre>
业务助手类： 
<pre class="layui-code">
class MyBusiness
{
    \DuckAdmin\Api\DuckAdminServiceApi::Foo();
}
</pre>

详细请了解控制器助手类有什么方法，请查阅 DuckAdmin, 代码
<br >
<h2>命令行</h2>

DuckAdmin 的高级功能都放在命令行里， 运行 --help 指令就能知道。
<pre class="layui-code">
duckphp-project --help
</pre>


</pre>
<h2>覆盖相应类的实现</h2>
根据 DuckPhp 系统 在你的 初始化 onInit 里添加替换的 
<pre class="layui-code">

class App
{
    public function onInit()
    {
        \DuckAdmin\Controller\Profile::G(MyProfile::G());
        \DuckAdmin\Service\ProfileService::G(MyProfileService::G());
    }
}
</pre>
<!-- -->

		</div>
		<div class="layui-col-md4">
		<form method="post" class="layui-form" style="border:1px solid; margin:1em;">
            <div class="title" style="font-size:big;background-color:#5FB878;"><h1>登录</h1></div>
            <div style="padding:1em;">
			<div class="layui-form-item">
				<input placeholder="账 户" type="text" name="username" hover class="layui-input"/>
			</div>
			<div class="layui-form-item">
				<input placeholder="密 码" type="password" name="password"  hover class="layui-input"/>
            </div>
            <div class="layui-form-item">
				<input placeholder="验证码 : " type="text" maxlength="4" name="captcha" hover class="code layui-input layui-input-inline"/>
				<a href="javascript:;" onclick="refresh_captcha()"><img id="codeimg" class="codeImage" src="<?=__url('captcha')?>" /></a>
                <script>
                function refresh_captcha(){
                    document.getElementById('codeimg').src=document.getElementById('codeimg').src
                }
              </script>
            </div>
            <div class="layui-form-item">
				<input type="checkbox" name="remember" title="30天内自动登录" lay-skin="primary" checked>
			</div>
			<div class="layui-form-item">
				<button class="login" lay-submit lay-filter="login">
					登 入
				</button>
			</div>
            </div>
		</form>
        </div>
    </div>
<!-- -->


<script src="/layui/layui.js"></script>
<script>
var error=<?=json_encode($error)?>;
</script>
<script>
layui.use('layer', function(){
  var layer = layui.layer;
  if(error){
  layer.msg(error,{icon:2});
  }
});     

</script>
  </div>
<div class="layui-footer" style ="background-color:#FAFAFA;padding:1em;text-align:center;">
感谢 LayUI
</div>
	</body>
</html>
