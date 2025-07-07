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

                    this.menuRender(param.menu);
                    var url_home = "account/dashboard";
                    this.bodyRender(url_home);
                }.bind(this));
            }
            this.menuRender = function(menu) {
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
            }

            this.bodyRender = function(url_home) {
                body.on("click", ".refresh", function() {
                    refresh();
                })
                bodyFrame = pearFrame.render({
                    elem: 'content',
                    title: '首页',
                    url: url_home,
                    width: '100%',
                    height: '100%'
                });

                
            }
            this.collapse = function(param) {
                if ($(window).width() >= 768) {
                    collapse()
                }
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

        function refresh() {
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
            }, 600)
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

        body.on("click", ".logout", function() {
            logout();
        })

        body.on("click", ".collapse,.pear-cover", function() {
            collapse();
        });

        body.on("click", ".menuSearch", function () {
//menuSearcher.open();return;
            // 过滤菜单
            var filterHandle = function (filterData, val) {
                if (!val) return [];
                var filteredMenus = [];
                filterData = $.extend(true, {}, filterData);
                $.each(filterData, function (index, item) {
                    if (item.children && item.children.length) {
                        var children = filterHandle(item.children, val)
                        var obj = $.extend({}, item, { children: children });
                        if (children && children.length) {
                            filteredMenus.push(obj);
                        } else if (item.title.indexOf(val) >= 0) {
                            item.children = []; // 父级匹配但子级不匹配,就去除子级
                            filteredMenus.push($.extend({}, item));
                        }
                    } else if (item.title.indexOf(val) >= 0) {
                        filteredMenus.push(item);
                    }
                })
                return filteredMenus;
            }

            // 树转路径
            var tiledHandle = function (data) {
                var tiledMenus = [];
                var treeTiled = function (data, content) {
                    var path = "";
                    var separator = " / ";
                    // 上级路径
                    if (!content) content = "";
                    $.each(data, function (index, item) {
                        if (item.children && item.children.length) {
                            path += content + item.title + separator;
                            var childPath = treeTiled(item.children, path);
                            path += childPath;
                            if (!childPath) path = ""; // 重置路径
                        } else {
                            path += content + item.title
                            tiledMenus.push({ path: path, info: item });
                            path = ""; //重置路径
                        }
                    })
                    return path;
                };
                treeTiled(data);

                return tiledMenus;
            }

            // 创建搜索列表
            var createList = function (data) {
                var _listHtml = '';
                $.each(data, function (index, item) {
                    _listHtml += '<li smenu-id="' + item.info.id + '" smenu-icon="' + item.info.icon + '" smenu-url="' + item.info.href + '" smenu-title="' + item.info.title + '" smenu-type="' + item.info.type + '">';
                    _listHtml += '  <span><i style="margin-right:10px" class=" ' + item.info.icon + '"></i>' + item.path + '</span>';
                    _listHtml += '  <i class="layui-icon layui-icon-right"></i>';
                    _listHtml += '</li>'
                })
                return _listHtml;
            }
            var do_search = function() {
                var menuData = sideMenu.option.data;
                
                var $input = $(".menu-search-input-wrapper input");
                var $noData = $(".menu-search-no-data");
                var $list = $(".menu-search-list");
                
                var keywords = $input.val().trim(); // 搜索
                var filteredMenus = filterHandle(menuData, keywords);
                
                if(filteredMenus.length){
                    var tiledMenus = tiledHandle(filteredMenus);
                    var listHtml = createList(tiledMenus);
                    $noData.css("display", "none");
                    $list.html("").append(listHtml).children(":first").addClass("this")
                }else{
                    $list.html("");
                    $noData.css("display", "flex");
                }
            }
            var do_openmenu = function($this) {
                var menuId = $this.attr("smenu-id");
                var menuUrl = $this.attr("smenu-url");
                var menuTitle = $this.attr("smenu-title");
                var menuType = $this.attr("smenu-type");
                
                var openableWindow = menuType === "1" || menuType === 1;

                if(sideMenu.isCollapse){
                    collapse();
                }
                if (openableWindow) {
                    pearAdmin.jump(menuId, menuTitle, menuUrl)  //这里要调整
                } else {
                    sideMenu.selectItem(menuId);
                }
            }
            layer.open({
                type: 1,
                offset: "10%",
                area: ['600px'],
                title: false,
                closeBtn: 0,
                shadeClose: true,
                anim: 0,
                move: false,
                content: $('#id-menu-search'),
                success: function(layero,layeridx){
                    var $layer = layero;
                    var $input = $(".menu-search-input-wrapper input");
                    var $list = $(".menu-search-list");
                    
                    $layer.css("border-radius", "6px");
                    $input.off("focus").focus();
                    // 搜索菜单
                    $input.off("input").on("input", debounce(function(){
                        do_search();
                        
                        var $layer = layero;
                        var $content = $(layero).children('.layui-layer-content');
                        
                        var currentHeight = $(".menu-search-content").outerHeight()
                        
                        $layer.css("height", currentHeight);
                        $content.css("height", currentHeight);
                    }, 500)
                    )
                    // 搜索列表点击事件
                    $list.off("click").on("click", "li", function () {
                        var $this = $(this);
                        do_openmenu($this);
                        compatible();
                        layer.close(layeridx);
                    })

                    $list.off('mouseenter').on("mouseenter", "li", function () {
                        $(".menu-search-list li.this").removeClass("this");
                        $(this).addClass("this");
                    }).off("mouseleave").on("mouseleave", "li", function(){
                        $(this).removeClass("this");
                    })

                    // 监听键盘事件
                    // Enter:13 Spacebar:32 UpArrow:38 DownArrow:40 Esc:27
                    $(document).off("keydown").keydown(function (e) {
                        if (e.keyCode === 13 || e.keyCode === 32) {
                            e.preventDefault();
                            var $this = $(".menu-search-list li.this");
                            do_openmenu($this);
                            compatible();
                            layer.close(layeridx);
                        }else if(e.keyCode === 38){
                            e.preventDefault();
                            var prevEl = $(".menu-search-list li.this").prev();
                            $(".menu-search-list li.this").removeClass("this");
                            if(prevEl.length !== 0){
                                prevEl.addClass("this");
                            }else{
                                $list.children().last().addClass("this");
                            }
                        }else if(e.keyCode === 40){
                            e.preventDefault();
                            var nextEl = $(".menu-search-list li.this").next();
                            $(".menu-search-list li.this").removeClass("this");
                            if(nextEl.length !== 0){
                                nextEl.addClass("this");
                            }else{
                                $list.children().first().addClass("this");
                            }
                        }else if(e.keyCode === 27){
                            e.preventDefault();
                            layer.close(layeridx);
                        }
                    })
                }
            })
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
