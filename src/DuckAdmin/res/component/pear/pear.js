window.rootPath = (function (src) {
	src = document.currentScript
		? document.currentScript.src
		: document.scripts[document.scripts.length - 1].src;
	return src.substring(0, src.lastIndexOf("/") + 1);
})();

layui.config({
	base: rootPath + "module/",
	version: "3.10.0"
}).extend({
// 这里是主页就启用的
	admin: "admin", 	// 框架布局组件               Y
	menu: "menu",		// 数据菜单组件               Y
	frame: "frame", 	// 内容页面组件               Y
	tab: "tab",			// 多选项卡组件               Y
	drawer: "drawer",	// 抽屉弹层组件               Y
	popup:"popup",      // 弹层封装                   Y
	loading: "loading",		// 加载组件               Y
	theme: "theme",			// 主题转换               Y
	message: "message",     // 通知组件               Y
	fullscreen:"fullscreen",  //全屏组件              Y

// 这里是里面调用的
    xmSelect: "xm-select",	// 下拉多选组件 //变更
	iconPicker: "iconPicker",// 图标选择
	treetable:"treetable",   // 树状表格
// 这里是没见过的    
	dtree:"dtree",			// 树结构


	nprogress: "nprogress",  // 进度过渡
    select: "select",	// 下拉多选组件
    

	notice: "notice",	// 消息提示组件
	step:"step",		// 分布表单组件
	tag:"tag",			// 多标签页组件

	count:"count",			// 数字滚动
	topBar: "topBar",		// 置顶组件
	button: "button",		// 加载按钮
	card: "card",			// 数据卡片组件
	context: "context",		// 上下文组件
	http: "http",			// ajax请求组件
    toast: "toast"         // 消息通知
    
}).use(['layer'], function () {
});