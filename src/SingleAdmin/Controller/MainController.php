<?php declare(strict_types=1);
/**
 * DuckAdmin SingleAdmin - Main Controller
 * index() 首页同时作为登录页;已登录则 302 到 home
 */
namespace DuckAdmin\SingleAdmin\Controller;

use DuckPhp\Foundation\Controller\Helper;
use DuckAdmin\SingleAdmin\Business\AdminBusiness;

class MainController
{
    public function index()
    {

        $data = [];
        // 已登录直接去主页
        if (Helper::AdminId(false)) {
            Helper::Show302(Helper::Admin()->urlForHome());
            return;
        }
        // POST 处理登录
        if (Helper::IsPost()) {
            var_dump("ISPOST");
            try{
                Helper::Admin()->login(Helper::POST());
                return;
            }catch(\Exception $ex){
                $data['error'] = $ex->getMessage();
            }
        }
        Helper::Show($data, 'main');
    }

    public function logout()
    {
        Helper::Admin()->logout();
    }
}
