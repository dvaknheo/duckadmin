<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>控制后台</title>
		<meta name="renderer" content="webkit">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<link rel="stylesheet" href="<?=__res('component/pear/css/pear.css')?>" />
	</head>
	<body class="pear-container">
		<div>
            <pre>
                重载 AccountController::dashboard 以获得数据。
                
                AccountController::_(MyAccountController::_());
                或：
                MyAccountController::_OverrideParent(); 
                重载这个页面以设置你的系统
            </pre>
		</div>
        <!--
		<script src="<?=__res('component/layui/layui.js')?>"></script>
		<script src="<?=__res('component/pear/pear.js')?>"></script>
        -->
	</body>
</html>
