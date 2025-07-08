</div>
                </div>
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
<div style="display:none;"><!-- 隐藏层用于弹出 -->
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

        <!-- 框 架 初 始 化 -->
<script>
var g_data_menu;
var g_url_home;
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
<script>
g_data_menu = <?=__json($data_menu)?>;
g_url_home =  "account/dashboard";
</script>
    </body>
</html>