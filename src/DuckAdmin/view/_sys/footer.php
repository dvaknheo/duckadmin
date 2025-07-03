                </div>
            </div>
            <!-- 页脚 -->
            <div class="layui-footer layui-text">
                <span class="left">
                    Released under the MIT license.
                </span>
                <span class="center"></span>
            </div>
            <!-- 遮 盖 层 -->
            <div class="pear-cover"></div>
            <!-- 加 载 动 画 -->
            <div class="loader-main">
                <!-- 动 画 对 象 -->
                <div class="loader"></div>
            </div>
        </div>
        <!-- 移 动 端 便 捷 操 作 -->
        <div class="pear-collapsed-pe collapse">
            <a href="#" class="layui-icon layui-icon-shrink-right"></a>
        </div>
        <!-- 依 赖 脚 本 -->
        <script src="<?=__res('component/layui/layui.js')?>"></script>
        <script src="<?=__res('component/pear/pear.js')?>"></script>
        <script src="<?=__res('admin/js/common.js')?>"></script>
        <!-- 框 架 初 始 化 -->
        <script>

            // Admin
            window.Admin = {
                Account: {}
            };

            layui.use(["admin","jquery","popup","drawer"], function() {
                var $ = layui.$;
                var admin = layui.admin;
                var popup = layui.popup;

                admin.setConfigType("json");
                admin.setConfigPath("<?=__url('config/get')?>");

                admin.render();

                // 登出逻辑
                admin.logout(function(){
                    var url = "<?=__url('account/logout')?>";
                    fetch(url).then(response => {return response.json();}).then(res => {
                            if (res.code) {
                                return popup.error(res.msg);
                            }
                            popup.success("注销成功",function(){
                                location.reload();
                            })
                    });
                    return false;
                });
                var url = "<?=__url('account/info')?>";
                fetch(url).then(response => {return response.json();}).then(res => {
                    window.Admin.Account = res.data;
                });

                // 消息点击回调
                //admin.message(function(id, title, context, form) {});
            });

        </script>
    </body>
</html>