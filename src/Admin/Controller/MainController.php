<?php declare(strict_types=1);
/**
 * DuckPhp Admin System - Main Controller
 * 处理首页路由 /
 */
namespace DuckAdmin\Admin\Controller;

class MainController
{
    public function index()
    {
        Helper::Show([], 'admin/login');
    }
    public function login()
    {
        if (Helper::SERVER('REQUEST_METHOD') === 'POST') {
            return $this->doLogin();
        }
        
        //TODO GET 已登录则跳转首页
        //Helper::Show302(__url(''));
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
        //AdminAction
        Session::_()->logout();
        Helper::Show302(__url('login/login'));
    }
}
