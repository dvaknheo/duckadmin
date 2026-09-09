<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckAdmin\User\Controller;

class MainController
{
    /**
     * DuckUser 安装：GET 展示环境自检与安装按钮；POST 执行安装
     */
    public function index()
    {
        $data =[];
        $data['url_reg'] = Helper::User()->urlForRegist();
        $data['url_login'] = Helper::User()->urlForLogin();
        
        Helper::Show($data, 'main');
    }
    public function register()
    {
        $data = [];
        $data['url_register'] = Helper::User()->urlForRegist();

        if (!Helper::IsPOST()) {
            $data['csrf_field'] = Helper::_()->csrfField();
            Helper::Show($data, 'register');
            return;
        }
        try {
            Helper::ThrowOn(empty(Helper::AppOptions('user_provider')), "本用户系统已经关闭");
            Helper::User()->register(Helper::Post());
            return;
        } catch (\Exception $ex) {
            $data['error'] = $ex->getMessage();
        }
        $data['csrf_field'] = Helper::_()->csrfField();
        $data['name'] = __h(Helper::POST('name', ''));

        Helper::Show($data, 'register');
    }
    public function login()
    {
        $data = [];
        if (!Helper::IsPOST()) {
            $data['csrf_field'] = Helper::_()->csrfField();
            $data['url_login'] = Helper::User()->urlForLogin();
            $data['back_url'] =Helper::GET('b','');
            Helper::Show($data,'login');
            return;
        }
        try {
            Helper::ThrowOn(empty(Helper::AppOptions('user_provider')), "本用户系统已经关闭");
            Helper::User()->login(Helper::POST());
            //$back_url = Helper::GET('b','');
            return;
        } catch (\Exception $ex) {
            $data['error'] = $ex->getMessage();
        }

        $data['csrf_field'] = Helper::_()->csrfField();
        $data['name'] = __h(Helper::POST('name', ''));

        Helper::Show($data, 'login');
    }
    public function logout()
    {
        if (empty(Helper::AppOptions('user_provider'))) {
            Helper::Show302(Helper::User()->urlForHome());
            return;
        }
        Helper::User()->logout();
    }
}
