window.rootPath = (function (src) {
	src = document.currentScript
		? document.currentScript.src
		: document.scripts[document.scripts.length - 1].src;
	return src.substring(0, src.lastIndexOf("/") + 1);
})();

layui.config({
	base: rootPath + "module/",
	version: "3.10.1"
}).extend({
// 这里是主页就启用的
	admin: "admin", 	// 框架布局组件               Y
	menu: "menu",		// 数据菜单组件               Y
	frame: "frame", 	// 内容页面组件               Y
	popup:"popup",      // 弹层封装                   Y
    menuSearcher:"menuSearcher",   //

// 这里是里面调用的
    xmSelect: "xm-select",	// 下拉多选组件 //变更
	iconPicker: "iconPicker",// 图标选择
	treetable:"treetable",   // 树状表格
}).use(['layer'], function () {
});