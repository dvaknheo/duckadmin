<?php declare(strict_types=1);
/**
 * DuckAdmin DemoUsers - Main Controller
 * index() 首页同时作为登录页;已登录则 302 到 home
 */
namespace DuckAdmin\DemoUsers\Controller;

class MainController
{
    public function index()
    {
        // 已登录直接去主页
        if (Session::_()->isLogin()) {
            Helper::Show302(__url(Helper::Options('user_url_home','Home/index')));
            return;
        }
        // POST 处理登录
        if (Helper::SERVER('REQUEST_METHOD', 'GET') === 'POST') {
            return $this->doLogin();
        }
        Helper::Show(get_defined_vars(), 'main');
    }

    private function doLogin()
    {
        try{
        $user = UserAction::_()->login(Helper::POST());
            Helper::Show302(__url(Helper::Options('user_url_home','Home/index')));
            return;
        }catch(\Exception $ex){
            Helper::Show(['error' => $ex->getMessage()], 'main');
        }
    }

    public function logout()
    {
        UserAction::_()->logout();
        Helper::Show302(__url(Helper::Options('user_url_login','')));
    }
}
