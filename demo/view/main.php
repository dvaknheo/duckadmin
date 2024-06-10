<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
        <link href="data:image/x-icon;base64,AAABAAEAEBAQAAEABAAoAQAAFgAAACgAAAAQAAAAIAAAAAEABAAAAAAAgAAAAAAAAAAAAAAAEAAAAAAAAADSx/8AAAAAAAAA/wAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAERERERERERERERESERERERERESAhERERERESAAIRERERESAAACERERESAAAAAhERESAAAAAAIRESAAAAAAACERIAAAAAAAIREgAAAAAAAhESAAACAAACEREgACEgACERERIiERIiERERERERERERERERERERERERERERERERERH//wAA/v8AAPx/AAD4PwAA8B8AAOAPAADABwAAgAMAAIADAACAAwAAgAMAAMEHAADjjwAA//8AAP//AAD//wAA" rel="icon" type="image/x-icon">
		<title>DuckAdmin 后台系统</title>
	</head>
	<body>
<div class="layui-header" style ="background-color:#5FB878;padding:1em;">
    <div>
        <h1>DuckAdmin Demo</h1><span> DuckPhp 的演示</span>
    </div>
</div>
  <div class="layui-container">
<!-- -->

    <div class="layui-row " style="border-left:1px solid;border-right:1px solid;padding:1em;">
		<div class="layui-col-md8" >
<!-- -->
<h1>欢迎使用 DuckAdmin Demo</h1>
<pre>
恭喜你已经把后台跑起来了.
demo 这个工程 ，演示了如何使用第三方的 duckphp 工程作为 库
但你不应该把本项目当成 `composer project` 来做二次开发，而是应该当成 `composer library` 引入。
然后分别使用组件

sqlite 数据库地址是 runtime/database.db。

如果你要重新安装，请运行  `php cli.php install --force`
或者
</pre>
<pre>
composer require dvaknheo/duckadmin
demo 只是给你提供了如何使用 duckadmin 这个库的方法。

duckadmin 这个 composer library 里 src/DuckAdmin 是个现成的管理模块。
你应该像 demo 那样使用他。

duckuser 则是附带的用户系统


（\SimpleBlog\System\SimpleBlogApp::class）
<a href="<?=$url_blog?>">一个简单的博客系统</a> 这个是接入其他前后台管理系统的一个应用案例。

（\DuckUser\System\DuckUserApp::class） <a href="<?=$url_user?>">用户基本注册登录页面在这里</a>
这是个很简陋的用户系统。提供了基本的用户接口。这个用户系统对应的

（\DuckAdmin\System\DuckAdminApp::class） 
<a href="<?=$url_admin?>">管理后台入口在这里</a> 
这个管理后台，模仿的是 webman admin 的后台，初始用户名是 admin 密码是 123456

（\DuckUserManager\System\DuckUserManagerApp::class）<a href="<?=$url_user_manager?>">用户管理后台在这里</a>
很简陋的用户管理系统。 (需要后台登录之后才能访问)。



</pre>

  </div>
<div class="layui-footer" style ="background-color:#FAFAFA;padding:1em;text-align:center;">
感谢 <a href="https://www.layui.com/"> LayUI </a> 前端支持，为我这个不懂得好看的能勉强做出来
感谢 pearadmin  让我可以 copy  idea.
感谢 webman admin 这个版本的从 webman admin 改出来的
</div>
	</body>
</html>