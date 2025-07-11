<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <title>主页 - <?php echo date("Y-m-d H:i:s"); ?></title>
        <!-- 依 赖 样 式 -->
        <link rel="stylesheet" href="<?=__res('component/pear/css/pear.css')?>" />
        <!-- 加 载 样 式 -->
        <link rel="stylesheet" href="<?=__res('admin/css/loader.css')?>" />
        <!-- 布 局 样 式 -->
        <link rel="stylesheet" href="<?=__res('admin/css/admin.css')?>" />
        <!-- 重置样式 -->
        <link rel="stylesheet" href="<?=__res('admin/css/reset.css')?>" />
    </head>
    <!-- 结 构 代 码 -->
    <body class="layui-layout-body pear-admin">
        <!-- 布 局 框 架 -->
        <div class="layui-layout layui-layout-admin">
            <!-- 顶 部 样 式 -->
            <div class="layui-header">
                <!-- 菜 单 顶 部 -->
                <!-- 顶 部 左 侧 功 能 -->
                <ul class="layui-nav layui-layout-left">
                    <li class="collapse layui-nav-item"><a href="#" class="layui-icon layui-icon-shrink-right"></a></li>
                    <li class="refresh layui-nav-item"><a href="#" class="layui-icon layui-icon-refresh-1" loading = 600></a></li>
                </ul>
                <!-- 多 系 统 菜 单 -->
                <div id="control" class="layui-layout-control"></div>
                <!-- 顶 部 右 侧 菜 单 -->
                <ul class="layui-nav layui-layout-right">
                    <li class="layui-nav-item"><a href="#" class="menuSearch layui-icon layui-icon-search"></a></li>
                    <li class="layui-nav-item user">
                        <!-- 头 像 -->
                        <a class="layui-icon layui-icon-username" href="javascript:;"></a>
                        <!-- 功 能 菜 单 -->
                        <dl class="layui-nav-child">
                            <dd><a user-menu-url="<?=__url('account/index')?>" user-menu-id="10" user-menu-title="基本资料">基本资料</a></dd>
                            <dd><a href="<?=__url('account/logout')?>" class="logout">注销登录</a></dd>
                        </dl>
                    </li>
                </ul>
            </div>
            <!-- 侧 边 区 域 -->
            <div class="layui-side layui-bg-black">
                <!-- 菜 单 顶 部 -->
                <div class="layui-logo">
                    <!-- 图 标 -->
                    <a href="#logo_click"><img class="logo" src="<?=__res('admin/images/logo.png')?>" alt="LOGO，请改正"></a>
                    <!-- 标 题 -->
                    <span class="title">【标题】</span>
                </div>
                <!-- 菜 单 内 容 -->
                <div class="layui-side-scroll">
                    <div id="sideMenu"></div>
                </div>
            </div>
            <!-- 视 图 页 面 -->
            <div class="layui-body">
                <!-- 内 容 页 面 -->
                <div id="content"></div>
            </div>
            <!-- 页脚 -->
            <div class="layui-footer layui-text">
                <span class="left">
                    Released under the MIT license.
                </span>
                <span class="center"></span>
            </div>
            <?php /*
            <!-- 遮 盖 层 -->
            <div class="pear-cover"></div>
            <!-- 加 载 动 画 -->
            <div class="loader-main">
                <!-- 动 画 对 象 -->
                <div class="loader"></div>
            </div>
            */?>
        </div>
        <!-- 移 动 端 便 捷 操 作 -->
        <div class="pear-collapsed-pe collapse">
            <a href="#" class="layui-icon layui-icon-shrink-right"></a>
        </div>
<div style="display:hidden"><!-- 隐藏层用于弹出 -->
<div class="menu-search-content" id ="id-menu-search">
  <div class="layui-form menu-search-input-wrapper">
    <div class=" layui-input-wrap layui-input-wrap-prefix">
      <div class="layui-input-prefix">
        <i class="layui-icon layui-icon-search"></i>
      </div>
      <input type="text" name="menuSearch" value="" placeholder="搜索菜单" autocomplete="off" class="layui-input" lay-affix="clear">
    </div>
  </div>
  <div class="menu-search-no-data">暂无搜索结果</div>
  <ul class="menu-search-list">
  </ul>
</div>
</div>
        <!-- 依 赖 脚 本 -->
        <script src="<?=__res('component/layui/layui.js')?>"></script>
        <script src="<?=__res('component/pear/pear.js')?>"></script>
        <script src="<?=__res('admin/js/common.js')?>"></script>
        <!-- 框 架 初 始 化 -->
<script>
var g_data_menu;
var g_url_home;
</script>
<script>
var g_data_menu = <?=__json($data_menu)?>;
var g_url_home =  "account/dashboard";
</script>

<script>
layui.use(["admin"], function() {
    var admin_data ={
        "url_home": g_url_home, //"account/dashboard",
        "menu": {
            "data":  g_data_menu, //"rule/get",
            "accordion": true,
            "collapse": false,
            "control": false,
            "controlWidth": 500,
            "select": 0,
            "async": true
        }
    };
    layui.admin.render(admin_data);
});
</script>

    </body>
</html>