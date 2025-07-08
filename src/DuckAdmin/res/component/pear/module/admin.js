layui.define(['jquery', 'element', 'form', 'menu', 'frame',"menuSearcher"],
    function(exports) {
        "use strict";

        var $ = layui.jquery,
        form = layui.form,
        element = layui.element,
        pearMenu = layui.menu,
        pearFrame = layui.frame;
        var menuSearcher = layui.menuSearcher ;

        var bodyFrame;
        var sideMenu;
        var bodyTab;
        var logout = function() {};
        var body = $('body');

        var pearAdmin = new function() {
            this.render = function(data_or_url) {
                var fetch_admin_and_run = function (object_or_url, callback) {
                    if (typeof object_or_url !== 'string') {
                        callback(object_or_url);
                        return;
                    }
                    fetch(object_or_url)
                        .then(response => { return response.json(); })
                        .then(res => { callback(res); });
                };
                fetch_admin_and_run(data_or_url,function(param){
                    var url_home = "account/dashboard";
                    /*
                    {
                        "data": "rule/get",
                        "accordion": true,
                        "collapse": false,
                        "control": false,
                        "controlWidth": 500,
                        "select": 0,
                        "async": true
                    }
                    */
                    var menu = param.menu;
                    sideMenu = pearMenu.render({
                        elem: 'sideMenu',
                        
                        theme: "dark-theme",
                        height: '100%',
                        control: false,  //menu.control
                        parseData: false,
                        
                        url: menu.data,
                        data: menu.data,
                        accordion: true,//param.menu.accordion,
                        async: menu.async !== undefined ? menu.async : true,
                        method: menu.method,
                        controlWidth: menu.controlWidth,
                        defaultMenu: 0,  //menu."select": 0,
                        
                        change: function() {
                            compatible();
                        },
                        done: function() {
                            sideMenu.isCollapse = menu.collapse;
                            sideMenu.selectItem(menu.select);
                            //pearAdmin.collapse(param);
                        }
                    });
                    sideMenu.click(function(dom, data) {
                        bodyFrame.changePage(data.menuUrl, true);
                        compatible()
                    })
                    
                    
                    bodyFrame = pearFrame.render({
                        elem: 'content',
                        title: '首页',
                        url: url_home,
                        width: '100%',
                        height: '100%'
                    });
                }.bind(this));
            }
            this.logout = function(callback) {
                logout = callback;
            }

            this.refresh = function(id) {
                $("iframe[id='"+ id +"']").attr('src', $("iframe[id='"+ id +"']").attr('src'));
            }
            this.jump = function(id, title, url) {
                sideMenu.selectItem(id);
                bodyFrame.changePage(url, true);
            }
        };

        function refreshInAdmin() {
            var refreshA = $(".refresh a");
            refreshA.removeClass("layui-icon-refresh-1");
            refreshA.addClass("layui-anim");
            refreshA.addClass("layui-anim-rotate");
            refreshA.addClass("layui-anim-loop");
            refreshA.addClass("layui-icon-loading");
            
            bodyFrame.refresh(true);  //改这里
            
            setTimeout(function() {
                refreshA.addClass("layui-icon-refresh-1");
                refreshA.removeClass("layui-anim");
                refreshA.removeClass("layui-anim-rotate");
                refreshA.removeClass("layui-anim-loop");
                refreshA.removeClass("layui-icon-loading");
            }, 600);
        }

        function collapse() {
            sideMenu.collapse();
            
            var admin = $(".pear-admin");
            var left = $(".layui-icon-spread-left")
            var right = $(".layui-icon-shrink-right")
            if (admin.is(".pear-mini")) {
                left.addClass("layui-icon-shrink-right")
                left.removeClass("layui-icon-spread-left")
                admin.removeClass("pear-mini");
                sideMenu.isCollapse = false;
            } else {
                right.addClass("layui-icon-spread-left")
                right.removeClass("layui-icon-shrink-right")
                admin.addClass("pear-mini");
                sideMenu.isCollapse = true;
            }
        }
        
        body.on("click", ".refresh", function() {
            refreshInAdmin();
        })

        body.on("click", ".logout", function() {
            logout();
        })

        body.on("click", ".collapse,.pear-cover", function() {
            collapse();
        });

        var  on_menu_searcher_click =function(menuId, menuTitle, menuUrl,menuType) {
            var openableWindow = menuType === "1" || menuType === 1;
            if(sideMenu.isCollapse){
                collapse();
            }
            if (openableWindow) {
                pearAdmin.jump(menuId, menuTitle, menuUrl)  //这里要调整
            } else {
                sideMenu.selectItem(menuId);
            }
            compatible();
        }
        body.on("click", ".menuSearch", function () {
            var menuData = sideMenu.option.data;
            menuSearcher.open({
                data:menuData,
                callback:on_menu_searcher_click,
            });

        });

        body.on("click", '[user-menu-id]', function() {
            bodyFrame.changePage($(this).attr("user-menu-url"), true);
        });
        function compatible() {
            if ($(window).width() <= 768) {
                collapse()
            }
        }
        $(window).on('resize', debounce(function () {
            if ($(window).width() <= 768) {
                collapse();
            }
        },50));

        function debounce(fn, awaitTime) {
            var timerID = null
            return function () {
                var arg = arguments[0]
                if (timerID) {
                    clearTimeout(timerID)
                }
                timerID = setTimeout(function () {
                    fn(arg)
                }, awaitTime)
            }
        }
        exports('admin', pearAdmin);
    })
