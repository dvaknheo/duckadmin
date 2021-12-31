<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>DuckAdmin 后台系统</title>
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
<pre>
恭喜你已经把后台跑起来了

但你不应该把本项目当成 project 来做二次开发，而是应该引入 library ，就像这个 工程那样。
<pre>
composer require dvaknheo/duckadmin
</pre>
然后调整相关入口代码。 <br />


<a href="<?=__url('admin/')?>">管理后台入口在这里（管理员用户名【admin】，密码为【123456】）</a>

<a href="<?=__url('user/')?>">用户基本注册登录页面在这里</a>

<a href="<?=__url('merchant/')?>">商家入口在这里。</a>

你可以只使用这一小部分，并在这上面改
</pre>
<hr />

调整代码的要点地方
<pre>
app 目录是工程目录

你要改入口 app/System/App.php
在配置里加上引入第三方的配置
</pre>
<hr >



  </div>
<div class="layui-footer" style ="background-color:#FAFAFA;padding:1em;text-align:center;">
感谢 <a href="https://www.layui.com/"> LayUI </a> 前端支持，为我这个不懂得好看的能勉强做出来
感谢 pearadmin  让我可以 copy  idea.
</div>
	</body>
</html>