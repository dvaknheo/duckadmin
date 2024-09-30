<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

$file = __DIR__.'/../../vendor/autoload.php';
if(is_file($file)){
    require_once $file;
}else{
    $file = __DIR__.'/../../../../autoload.php';
    if(is_file($file)){
        require_once $file;
    }
}
use DuckPhp\DuckPhp;
use DuckAdmin\System\DuckAdminApp;
use DuckPhp\Foundation\Helper;

//////////////////////////
//我们演示在其他框架系统中嵌入 duckadmin
$options=[
    'path' => __DIR__.'/../', // 指定路径
    'skip_404' => true,      // 跳过内部 404处理。
    'app' => [
        DuckAdminApp::class => [      // 后台管理系统
            'controller_url_prefix' => 'app/admin/',    // 访问路径
            'installed' => '2024-06-10T22:12:01+08:00', // 安装标志
            'database_list' =>  //指定数据库
            [
                'dsn' => 'sqlite:demodb.db',
                'username' => '',
                'password' => '',
            ],
        ],
    ],
    
    
];

$flag = DuckPhp::RunQuickly($options);
if ($flag) {
    return; // 后台管理系统完毕
}


//
//////////////////////////
// you code here.
$url_admin = __url('app/admin/index'); // 指向动态链接

$admin_id = Helper::AdminId(false);
$admin_name = $admin_id ? Helper::AdminName() : '';
$url_logout = Helper::Admin()->urlForLogout();

?>
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
<a href="/">回主页</a>
<hr />
这是另一个 demo，这模式下，你的代码和duckphp 的代码完全分离 <br />
<?php if($admin_id){?>
你好 <?=$admin_name?> (<?=$admin_id?>)
 <a href="<?=$url_logout?>">登出</a> 
<?php }else{?>
 <a href="<?=$url_admin?>">请登录</a> 
<?php }?>
</div>
<div>代码样例</div>
<pre style="border:1px solid red;">
use DuckPhp\DuckPhp;
use DuckAdmin\System\DuckAdminApp;
use DuckPhp\Foundation\Helper;

//////////////////////////
//我们演示在其他框架系统中嵌入 duckadmin
$options=[
    'path' => __DIR__.'/../', // 指定路径
    'skip_404' => true,      // 跳过内部 404处理。
    'app' => [
        DuckAdminApp::class => [      // 后台管理系统
            'controller_url_prefix' => 'app/admin/',    // 访问路径
            'installed' => '2024-06-10T22:12:01+08:00', // 安装标志
            'database_list' =>  //指定数据库
            [
                'dsn' => 'sqlite:demodb.db',
                'username' => '',
                'password' => '',
            ],
        ],
    ],
    
    
];

$flag = DuckPhp::RunQuickly($options);
if ($flag) {
    return; // 后台管理系统完毕
}


//
//////////////////////////
// you code here.
$url_admin = __url('app/admin/index'); // 指向动态链接

$admin_id = Helper::AdminId(false);
$admin_name = $admin_id ? Helper::AdminName() : '';
$url_logout = Helper::Admin()->urlForLogout();
</pre>
</div>
	</body>
</html>
