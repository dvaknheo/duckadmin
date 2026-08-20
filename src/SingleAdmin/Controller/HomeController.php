<?php declare(strict_types=1);
/**
 * DuckAdmin SingleAdmin - Home Controller
 * index() 登录后主页,带登出按钮
 */
namespace DuckAdmin\SingleAdmin\Controller;

use DuckPhp\Foundation\Controller\AdminControllerBase;

class HomeController extends AdminControllerBase
{
    public function index()
    {
        $data = [];
        try{
            Helper::Show($data, 'home');
        }catch(\Throwable $ex){
            echo "<pre>\n";
            echo $ex;
            echo "</pre>\n";
        }
    }
}
