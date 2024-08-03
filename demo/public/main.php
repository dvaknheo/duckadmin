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
@include_once(__DIR__. '/../LocalOverride.php');

//////////////////////////
//我们演示在其他框架系统中嵌入 duckadmin
$options=[
    'path' => __DIR__.'/../', // 指定路径
    
    'skip_404' => true,      // 跳过内部 404处理。
    'app' => [
        \DuckAdmin\System\DuckAdminApp::class => [      // 后台管理系统
            'controller_url_prefix' => 'app/admin/',    // 访问路径
        ],
    ]
];
$flag = \DuckPhp\DuckPhp::RunQuickly($options);
if ($flag) {
    return; // 后台管理系统完毕
}
//
//////////////////////////
// you code here.
$url_admin = __url('app/admin/index'); // 指向动态链接

?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
        <link href="data:image/x-icon;base64,AAABAAEAEBAQAAEABAAoAQAAFgAAACgAAAAQAAAAIAAAAAEABAAAAAAAgAAAAAAAAAAAAAAAEAAAAAAAAADSx/8AAAAAAAAA/wAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAERERERERERERERESERERERERESAhERERERESAAIRERERESAAACERERESAAAAAhERESAAAAAAIRESAAAAAAACERIAAAAAAAIREgAAAAAAAhESAAACAAACEREgACEgACERERIiERIiERERERERERERERERERERERERERERERERERH//wAA/v8AAPx/AAD4PwAA8B8AAOAPAADABwAAgAMAAIADAACAAwAAgAMAAMEHAADjjwAA//8AAP//AAD//wAA" rel="icon" type="image/x-icon">
		<title>DuckAdmin 后台系统</title>
	</head>
	<body>
<a href="/">回主页</a>
这是另一个 demo，这模式下，你的代码和duckphp 的代码完全分离
<pre> 
<a href="<?=$url_admin?>">管理后台入口在这里</a> 
</pre>
	</body>
</html>
