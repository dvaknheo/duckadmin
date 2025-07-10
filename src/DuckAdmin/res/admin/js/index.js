window.refreshTable = function(param) {
    layui.table.reloadData("data-table", {
        scrollPos: "fixed"
    });
}
function toggleSearchFormShow()
{
    let $ = layui.$;
    let items = $('.top-search-from .layui-form-item');
    if (items.length <= 2) {
        if (items.length <= 1) $('.top-search-from').parent().parent().remove();
        return;
    }
    let btns = $('.top-search-from .toggle-btn a');
    let toggle = toggleSearchFormShow;
    if (typeof toggle.hide === 'undefined') {
        btns.on('click', function () {
            toggle();
        });
    }
    let countPerRow = parseInt($('.top-search-from').width()/$('.layui-form-item').width());
    if (items.length <= countPerRow) {
        return;
    }
    btns.removeClass('layui-hide');
    toggle.hide = !toggle.hide;
    if (toggle.hide) {
        for (let i = countPerRow - 1; i < items.length - 1; i++) {
            $(items[i]).hide();
        }
        return $('.top-search-from .toggle-btn a:last').addClass('layui-hide');
    }
    items.show();
    $('.top-search-from .toggle-btn a:first').addClass('layui-hide');
}

function enable_table_events(table){
    // 通用事件
    table.on("toolbar(data-table)", function(obj) {
        if (obj.event === "refresh") {
            window.refreshTable();
        } else if (obj.event === "batchRemove") {
            batchRemove(obj);
        }
    });
    // 表格排序事件
    table.on("sort(data-table)", function(obj){
        table.reload("data-table", {
            initSort: obj,
            scrollPos: "fixed",
            where: {
                field: obj.field,
                order: obj.type
            }
        });
    });
}
function enable_open_layer()
{
    let $=layui.$;
    $("#js-main").on('click','.js-open-layer',function(e){
        e.preventDefault();
        var url = $(this).attr('href');
        var title =$(this).attr('alt');
        var width =common_isModile()?"100%":"500px";
        var height = common_isModile()?"100%":"450px";
        layer.open({
            type: 2,
            title: title,
            shade: 0.1,
            area: [width,height],
            content: url
        });
    });
}
function enable_delete_link()
{
    let $=layui.$;
    $("#js-main").on('click','.js-delete',function(e){
        e.preventDefault();
        var originalUrl = $(this).attr('href');
        const [baseUrl, queryString] = originalUrl.split('?');
    
    // 解析查询参数
    const params = {};
    if (queryString) {
        queryString.split('&').forEach(pair => {
            const [key, value] = pair.split('=');
            if (key) params[key] = value || '';
        });
    }
        layui.layer.confirm("确定删除?", {
            icon: 3,
            title: "提示"
        }, function(index) {
            layui.layer.close(index);
            let loading = layer.load();
            $.ajax({
                url: "delete",
                data: params,
                dataType: "json",
                type: "post",
                success: function(res) {
                    layui.layer.close(loading);
                    if (res.code) {
                        return layui.popup.failure(res.msg);
                    }
                    return layui.popup.success("操作成功", refreshTable);
                }
            })
        });
    });
}

// 删除多行
function batchRemove(obj) {
    let $=layui.$;
    let data = layui.table.checkStatus(obj.config.id).data;
    if (!data.length) {
        layui.popup.warning("未选中数据");
        return false;
    }
    var ids =[];
    for (let i = 0; i < data.length; i++) {
        ids.push(data[i].id);
    }
    var params={};
    params['id']=ids;
    layer.confirm("确定删除?", {
        icon: 3,
        title: "提示"
    }, function(index) {
        layer.close(index);
        let loading = layer.load();
        $.ajax({
            url: "delete",    //TODO 这里要更通用化。
            data: params,
            dataType: "json",
            type: "post",
            success: function(res) {
                layer.close(loading);
                if (res.code) {
                    return layui.popup.failure(res.msg);
                }
                return layui.popup.success("操作成功", refreshTable);
            }
        })
    });
}



function enable_index_page_all(table){
    enable_table_events(table);
    enable_open_layer();
    enable_delete_link();
}