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

但你不应该把本项目当成 `composer project` 来做二次开发，而是应该引入 `composer library` 
<pre>
composer require dvaknheo/duckadmin
</pre>
你一定会见过很多后台系统，都是在那基础上搞二次开发。
如果你实在你太懒，对代码里的 DuckPhpDemo 命名空间足够无视，你也可以和他们那样搞。
但是，这将不是个符合 duckphp 思维的项目。
demo 只是给你提供了如何使用 duckadmin 这个库的方法。

duckadmin 这个 composer library 里 src/DuckPhp 是个现成的管理模块。
你应该像 duckphpdemo 那样使用他。
duckuser 则是附带的用户系统
duckmerchant 则是一个商户系统。

duckadmindemo 这个 工程，演示了如何使用第三方的 duckphp 工程作为 库


安装



<a href="<?=__url('admin/index')?>">管理后台入口在这里（管理员用户名【admin】，密码为【123456】）</a>

<a href="<?=__url('user/')?>">用户基本注册登录页面在这里</a>

<a href="<?=__url('merchant/')?>">商家入口在这里。</a>

你可以只使用这一小部分，并在这上面改
</pre>


  </div>
<div class="layui-footer" style ="background-color:#FAFAFA;padding:1em;text-align:center;">
感谢 <a href="https://www.layui.com/"> LayUI </a> 前端支持，为我这个不懂得好看的能勉强做出来
感谢 pearadmin  让我可以 copy  idea.
</div>
	</body>
</html>