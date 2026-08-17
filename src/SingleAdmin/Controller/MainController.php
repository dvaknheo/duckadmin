<?php declare(strict_types=1);
/**
 * DuckAdmin SingleAdmin - Main Controller
 * index() 首页同时作为登录页;已登录则 302 到 home
 */
namespace DuckAdmin\SingleAdmin\Controller;

use DuckPhp\Core\App;

class MainController extends Base
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
        $password_ok = (string)(App::_()->options['single_admin_password'] ?? '');

        if ($username === 'admin' && $password !== '' && $password === $password_ok) {
            Session::_()->setLogin($username);
            Helper::Show302(__url('Home/index'));
            return;
        }
        Helper::Show(['error' => '用户名或密码错误'], 'main');
    }

    public function logout()
    {
        Session::_()->logout();
        Helper::Show302(__url('index'));
    }
}
