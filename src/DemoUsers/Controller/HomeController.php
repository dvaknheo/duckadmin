<?php declare(strict_types=1);
/**
 * DuckAdmin DemoUsers - Home Controller
 * index() 登录后主页,带登出按钮
 */
namespace DuckAdmin\DemoUsers\Controller;

class HomeController extends Base
{
    public function index()
    {
        // 未登录跳登录页
        if (!Session::_()->isLogin()) {
            Helper::Show302(__url('index'));
            return;
        }
        Helper::Show(get_defined_vars(), 'home');
    }
}
