/**
 * 浏览页面顶部搜索框展开收回控制
 */
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

/**
 * 获取控制器详细权限，并决定展示哪些按钮或dom元素
 */
function togglePermission() {
    let $ = layui.$;
    the_url = window.PERMISSION_API ? window.PERMISSION_API : "/app/admin/rule/permission";
    $.ajax({
        url: the_url,
        dataType: "json",
        success: function (res) {
            let style = '';
            let codes = res.data || [];
            let isSupperAdmin = false;
            // codes里有*，说明是超级管理员，拥有所有权限
            if (codes.indexOf('*') !== -1) {
                $("head").append("<style>*[permission]{display: initial}</style>");
                isSupperAdmin = true;
            }
            if (self !== top) {
                top.Admin.Account.isSupperAdmin = isSupperAdmin;
            } else {
                window.Admin.Account.isSupperAdmin = isSupperAdmin;
            }
            if (isSupperAdmin) return;

            // 细分权限
            layui.each(codes, function (k, code) {
                codes[k] = '*[permission^="'+code+'"]';
            });
            if (codes.length) {
                $("head").append("<style>" + codes.join(",") + "{display: initial}</style>");
            }
        }
    });

}
function ajax_post(form, callback) {
    const action = form.getAttribute('action') || location.pathname;
    const method = form.getAttribute('method') || 'GET';
    const formData = new FormData(form);
    return fetch(action, {
        method: method,
        body: formData,
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json(); // 解析 JSON
    })
    .then(data => {
        if (typeof callback === 'function') {
            callback(data); // 成功时调用 callback(data)
        }
        return data; // 仍然返回 data 以便链式调用
    })
    .catch(error => {
        console.error('Fetch error:', error);
        throw error; // 继续抛出错误，以便外部可以 .catch()
    });
}

function fill_form(data)
{
    let $ = layui.$;
    // 赋值表单
    layui.each(data, function (key, value) {
        let obj = $('*[name="'+key+'"]');
        if (key === "password") {
            obj.attr("placeholder", "不更新密码请留空");
            return;
        }
        if (typeof obj[0] === "undefined" || !obj[0].nodeName) return;
        if (obj[0].nodeName.toLowerCase() === "textarea") {
            obj.html(layui.util.escape(value));
        } else {
            obj.attr("value", value);
        }
    });
}

common_checkField = function(obj, field) {
    let data = layui.table.checkStatus(obj.config.id).data;
    if (data.length === 0) {
        return "";
    }
    let ids = "";
    for (let i = 0; i < data.length; i++) {
        ids += data[i][field] + ",";
    }
    ids = ids.substr(0, ids.length - 1);
    return ids;
}

/**
 * 当前是否为与移动端
 * */
common_isModile = function(){
    if ($(window).width() <= 768) {
        return true;
    }
    return false;
}