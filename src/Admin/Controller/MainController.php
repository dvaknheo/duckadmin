<?php declare(strict_types=1);
/**
 * DuckPhp Admin System - Main Controller
 * 处理首页路由 /
 */
namespace DuckAdmin\Admin\Controller;

class MainController
{
    public function __construct()
    {
        $this->initController();
    }
    protected function initController()
    {
        Helper::checkInstall();
    }
    public function index()
    {
        Helper::Show([], 'admin/login');
    }
    public function login()
    {
        $data = [];
        if (Helper::IsPost()) {
            try{
                Helper::ThrowOn(empty(Helper::AppOptions('admin_provider')), "本登录系统已经关闭");

                $username = Helper::POST('username', '');
                $password = Helper::POST('password', '');
                Helper::ThrowOn((empty($username) || empty($password)), '请输入用户名和密码');

                AuthAction::_()->login(Helper::POST());
                return;
            }catch(\Exception $ex) {
                $data['error'] = $ex->getMessage();
            }
        }        
        Helper::Show($data, 'admin/login');
    }
    public function logout()
    {
        AuthAction::_()->logout();
    }
}
