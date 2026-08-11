<?php declare(strict_types=1);
/**
 * DuckPhp Admin System - Login Controller
 * 路由: Login/login (GET 显示表单, POST 处理登录)
 *       Login/logout (退出)
 */
namespace DuckAdmin\Admin\Controller;

use DuckAdmin\Admin\Business\AuthBusiness;
use \DuckPhp\Foundation\ControllerTrait;

class LoginController
{
    use ControllerTrait;
    
    public function __construct()
    {
        Helper::checkInstall();
    }
    
    /**
     * 登录页面 / 处理登录
     * GET 显示表单，POST 处理登录
     */
    public function login()
    {
        // POST 处理登录
        if (Helper::SERVER('REQUEST_METHOD', '') === 'POST') {
            return $this->doLogin();
        }
        
        // GET 已登录则跳转首页
        if (Session::_()->isLogin()) {
            Helper::Show302(__url(''));
        }
        Helper::Show(get_defined_vars(), 'admin/login');
    }
    
    /**
     * 处理登录 POST
     */
    private function doLogin()
    {
        $username = Helper::POST('username', '');
        $password = Helper::POST('password', '');
        
        if (empty($username) || empty($password)) {
            Helper::Show(['error' => '请输入用户名和密码'], 'admin/login');
            return;
        }
        
        $result = AuthBusiness::_()->verify($username, $password);
        
        if ($result['success']) {
            Session::_()->setLogin($result['user_id'], $username, $result['realname']);
            Helper::Show302(__url(''));
        } else {
            Helper::Show(['error' => $result['message']], 'admin/login');
        }
    }
    
    /**
     * 退出登录
     */
    public function logout()
    {
        Session::_()->logout();
        Helper::Show302(__url('login/login'));
    }
}
