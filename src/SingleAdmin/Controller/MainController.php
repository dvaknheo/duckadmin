<?php declare(strict_types=1);
/**
 * DuckAdmin SingleAdmin - Main Controller
 * index() 首页同时作为登录页;已登录则 302 到 home
 */
namespace DuckAdmin\SingleAdmin\Controller;

use DuckPhp\Foundation\Controller\ControllerHelper as Helper;
use DuckPhp\GlobalAdmin\GlobalAdmin;

class MainController
{
    public function index()
    {
        $data = [];
        if (Helper::AdminId(false)) {
            $url = Helper::Admin()->urlForHome();
            Helper::Show302($url);
            return;
        }
        
        if (Helper::IsPost()) {
            try{
                GlobalAdmin::_()->login(Helper::POST());
                return;
            }catch(\Exception $ex){
                $data['error'] = $ex->getMessage();
            }
        }
        Helper::Show($data, 'main');
    }

    public function logout()
    {
        GlobalAdmin::_()->logout();
    }
}
