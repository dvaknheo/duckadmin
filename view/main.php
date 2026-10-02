<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
        <link href="data:image/x-icon;base64,AAABAAEAEBAQAAEABAAoAQAAFgAAACgAAAAQAAAAIAAAAAEABAAAAAAAgAAAAAAAAAAAAAAAEAAAAAAAAAAAAAAAPDw8AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAERERERERERERERESERERERERESAhERERERESAAIRERERESAAACERERESAAAAAhERESAAAAAAIRESAAAAAAACIRIAAAAAAAIREgAAAAAAAhESAAACAAACEREgACEgACERERIiERIiERERERERERERERERERERERERERERERERERH//wAA/v8AAPx/AAD4PwAA8B8AAOAPAADABwAAgAMAAIADAACAAwAAgAMAAMEHAADjjwAA//8AAP//AAD//wAA" rel="icon" type="image/x-icon">
		<title>DuckAdmin 后台系统</title>
        <style>
            .status-bar { background:#f5f5f5; border-bottom:1px solid #ddd; padding:8px 16px; font-size:14px; }
            .status-bar .section { display:inline-block; margin-right:24px; }
            .status-bar .label { color:#666; }
            .status-bar .value { color:#333; font-weight:bold; }
            .status-bar a { text-decoration:none; color:#1E9FFF; margin-left:8px; }
            .status-bar a:hover { text-decoration:underline; }
        </style>
	</head>
	<body>
<div class="layui-header" style="background-color:#5FB878;padding:1em;">
    <div>
        <h1>DuckAdmin Demo</h1><span> DuckPhp 的演示</span>
    </div>
</div>

<!-- 状态栏：管理员状态 + 用户状态 -->
<div class="status-bar">
    <!-- 管理员状态 -->
    <div class="section">
        <span class="label">管理员:</span>
        <?php if ($admin_id): ?>
            <span class="value"><?=htmlspecialchars($admin_name ?: $admin_id)?></span>
            <a href="<?=$url_admin_home?>">后台</a>
            <a href="<?=$url_admin_logout?>">登出</a>
        <?php else: ?>
            <span class="value">未登录</span>
            <a href="<?=$url_admin_login?>">登录</a>
        <?php endif; ?>
    </div>

    <!-- 用户状态 -->
    <div class="section">
        <span class="label">用户:</span>
        <?php if ($user_id): ?>
            <span class="value"><?=htmlspecialchars($user_name ?: $user_id)?></span>
            <a href="<?=$url_user_home?>">主页</a>
            <a href="<?=$url_user_logout?>">登出</a>
        <?php else: ?>
            <span class="value">未登录</span>
            <a href="<?=$url_user_login?>">登录</a>
            <a href="<?=$url_user_register?>">注册</a>
        <?php endif; ?>
    </div>
</div>

  <div class="layui-container" style="padding:1em;">
<!-- 现有内容已隐藏
    <div class="layui-row " style="border-left:1px solid;border-right:1px solid;padding:1em;">
		<div class="layui-col-md8" >
<h1>欢迎使用 DuckAdmin Demo</h1>
<pre>
恭喜你已经把后台跑起来了.
demo 这个工程 ，演示了如何使用第三方的 duckphp 工程作为 库
但你不应该把本项目当成 `composer project` 来做二次开发，而是应该当成 `composer library` 引入。
然后分别使用组件
</pre>
<pre>
duckadmin 这个 composer library 里 src/DuckAdmin 是个现成的管理模块。
duckuser 则是附带的用户系统

（\DuckAdmin\System\DuckAdminApp::class）
<a href="<?=$url_admin?>">管理后台入口在这里</a>

（\SimpleBlog\System\SimpleBlogApp::class）
<a href="<?=$url_blog?>">一个简单的博客系统</a> 这个是接入其他前后台管理系统的一个应用案例。

（\DuckUser\System\DuckUserApp::class） <a href="<?=$url_user?>">用户基本注册登录页面在这里</a>
这是个很简陋的用户系统。提供了基本的用户接口。这个用户系统对应的

</pre>
选择接入的 管理员系统

<label><input type="radio" name ="admin">硬编码的管理员系统 （\DuckAdmin\SingleAdmin\System\SingleAdminApp::class）</label>
<label><input type="radio" name ="admin" >基本管理员系统（\DuckAdmin\Admin\System\AdminApp::class）</select></label>
ThinkPHP ORM 版基本用户系统 (需要 composer require topthink/think-orm)
Laravel ORM 版基本用户系统 (需要 composer require laravel/framework)

选择接入的 用户系统
<label><input type="radio" name ="user" >简单硬编码的用户系统 （\DuckAdmin\DemoUsers\System\SingleAdminApp::class）</select></label>
<label><input type="radio" name ="user" >基本用户系统 （\DuckAdmin\DemoUsers\System\SingleAdminApp::class）</select></label>
<hr>
<div>
  <div class="layui-container">
</div>

</div>
</div>
  </div>
-->
	</body>
</html>
