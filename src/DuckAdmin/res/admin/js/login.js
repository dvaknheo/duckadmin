layui.use(['form', 'popup', 'layer'], function() {
    layer = layui.layer;
    function switchCaptcha() {
        var  url_captcha = document.querySelector('.codeImage').getAttribute('src-ref');
        document.querySelector('.codeImage').setAttribute("src", url_captcha + new Date().getTime());
    }
    document.querySelector('.codeImage').addEventListener('click', function () {
        switchCaptcha();
    });
    switchCaptcha();
    // 登 录 提 交
    layui.form.on('submit(login)', function (data) {
        ajax_post(this.closest('form'), function (res) {
            layer.closeAll('loading');
            if (res.code) {
                layui.popup.failure(res.msg, function () {
                    switchCaptcha();
                });
                return;
            }
            layui.popup.success('登录成功', function () {
                if(url_back){
                    location.href = url_back;
                    return;
                }
                location.reload();
            })
        });
        return false;
    });

});
