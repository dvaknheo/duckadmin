<?php declare(strict_types=1);
/**
 * DuckAdmin DemoUsers - Main Controller
 * index() 首页同时作为登录页;已登录则 302 到 home
 */
namespace DuckAdmin\DemoUsers\Controller;

use DuckAdmin\DemoUsers\Business\UserService;

class MainController
{
    public function index()
    {
        // 已登录直接去主页
        if (Session::_()->isLogin()) {
            Helper::Show302(__url('Home/index'));
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
        $username = (string)Helper::POST('username', '');
        $password = (string)Helper::POST('password', '');

        $user = UserService::_()->verifyLogin($username, $password);
        if ($user) {
            Session::_()->setCurrentUser($user);
            Helper::Show302(__url(Helper::Options('user_url_home','Home/index')));
            return;
        }
        Helper::Show(['error' => '用户名或密码错误'], 'main');
    }

    public function logout()
    {
        //TODO 触发事件
        Session::_()->unsetCurrentUser();
        Helper::Show302(__url(Helper::Options('user_url_login','')));
    }
}
