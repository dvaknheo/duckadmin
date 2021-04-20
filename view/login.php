<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>后台管理系统</title>
		<link href="/layui/css/layui.css" rel="stylesheet" />
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
	background-color: #5FB878 !important;
	font-weight: 550 !important;
}

.layui-form-checked[lay-skin=primary] i {
	border-color: #5FB878 !important;
	background-color: #5FB878 !important;
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

.layui-input:focus {
	box-shadow: 0px 0px 3px 1px #5FB878 !important;
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
	color: #5FB878 !important;
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
	background-color: whitesmoke;
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

</style>
	</head>
	<body style="background-size: cover;">
		<form method="post" class="layui-form">
			<div class="layui-form-item">
				<img class="logo" src="???" />
				<div class="title">DuckAdmin</div>
				<div class="desc">
					只 是 拿 来 用 一 下
				</div>
			</div>
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
		</form>

    </body>
</html>