<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title></title>
		<link href="/layui/css/layui.css" rel="stylesheet" />
        <style>


        </style>
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

<pre>
我们还应该添加是否安装的提示


这里是更多说明
目前系统运行方式为
[V] 直接运行
[X] 插件模式运行

请注意 DuckAdmin 是 Composer Library 。而不是 Composer Project 。
简而言之，请勿在 DuckAdmin 作为你的桩代码来修改，而是在你的工程里引入。

DuckAdmin 是用来给 DuckPhp 项目做二次开发用的。

你应该在调整选项
```php
$options['ext'][\DuckAdmin\App\App::class] = [];

```

安装要点：




数据库前缀。


使用要点

你后台的控制器类， 
或者在你的控制器类构造函数里加

\DuckAdmin\App\ControllerHelper::CheckPermission(); 检查权限

或者
extends \DuckAdmin\Controller\BaseController



\DuckAdmin\App\ControllerHelper::CheckPermission(); 检查权限


魔改 DuckAdmin

最必须的魔改： 这个首页要替换
在你的
建立了：

view\DuckAdimin\index.php 

这个文件，重新设立你的入口页面。



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
				<img id="codeimg" class="codeImage" />
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
  </div>
	</body>
</html>
