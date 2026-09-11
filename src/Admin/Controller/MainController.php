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
        Helper::Show([], 'Main/login');
    }
    public function login()
    {
        $data = [];
        if (Helper::IsPost()) {
            try{

                AuthAction::_()->login(Helper::POST());
                return;
            }catch(\Exception $ex) {
                $data['error'] = $ex->getMessage();
            }
        }        
        Helper::Show($data, 'Main/login');
    }
    public function logout()
    {
        AuthAction::_()->logout();
    }
}
