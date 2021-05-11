<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title></title>
		<link href="/layui/css/layui.css" rel="stylesheet" />
        <style>
        
.content {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 100%;
    text-align: center;
}
.content-r>h1 {
    font-size: 72px;
    color: #434e59;
    margin-bottom: 24px;
    font-weight: 600;
}

.content-r>p {
	font-size: 20px;
	color: rgba(0, 0, 0, .45);
	margin-bottom: 16px;
}

button {
	margin-top: 20px;
}
.pear-btn {
	display: inline-block;
	line-height: 38px;
	white-space: nowrap;
	cursor: pointer;
	text-align: center;
	box-sizing: border-box;
	outline: none;
	transition: 0.1s;
	font-weight: 500;
	padding: 0 18px;
	height: 38px;
	font-size: 14px;
	color: #2f495e;
	background-color: #edf2f7;
	border-radius: 4px;
	border: none;
	box-shadow: 0 1px 3px 0 rgba(0, 0, 0, .1), 0 1px 2px 0 rgba(0, 0, 0, .06);
}

.pear-btn i {
	font-size: 13px;
}

.pear-btn:hover {
	opacity: .8;
	filter: alpha(opacity=80);
	color: #409eff;
	border-color: #c6e2ff;
	background-color: #ECF5FF;
}

.pear-btn-primary {
	background-color:#5FB878!important;
	border: #2D8CF0;
}
        </style>
	</head>
	<body>
		<div class="content" style="top: 30%;">
			<div class="content-r">
				<h1>Pear Admin Think</h1>
                <div></div>
				<p>欢迎使用</p>
				<button class="pear-btn pear-btn-primary" style=" "><a href ="<?=__url('login')?>">登录-</a></button>
			</div>
		</div>
	</body>
</html>
