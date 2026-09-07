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
        $data = [];
        if (!Helper::UserId(false)) {
            Helper::Show302(Helper::User()->urlForHome());
            return;
        }
        // POST 处理登录
        if (Helper::IsPost()) {
            try{
                $user = Helper::User()->login(Helper::POST());
                return;
            }catch(\Exception $ex){
                Helper::Show(['error' => $ex->getMessage()], 'main');
            }
        }
        Helper::Show($data, 'main');
    }
    public function logout()
    {
        Helper::User()->logout();
    }
}
