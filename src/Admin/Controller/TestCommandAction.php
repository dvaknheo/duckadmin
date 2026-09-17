<?php declare(strict_types=1);
/**
 * DuckPhp Admin System - App Action
 * Web 安装器(RouteHookWebInstaller)的自定义回调，Controller 层
 */
namespace DuckAdmin\Admin\Controller;

use DuckAdmin\Admin\Business\AdminTreeBuilder;
use DuckAdmin\Admin\Business\AppBusiness;
use DuckAdmin\Admin\Business\TestBusiness;
use DuckAdmin\Admin\Model\PermissionModel;
use DuckPhp\Foundation\SingletonTrait;

class TestCommandAction
{
    use SingletonTrait;

    /**
     * 重新在数据库里生成权限菜单
     * @return void
     */
    public function command_rebuild_menu()
    {
        $ret = TestBusiness::_()->rebuild_menu();

        echo "\n".DATE(DATE_ATOM)."\n";
    }
    /**
     * test something
     */
    public function command_cache_menu()
    {
        $ret = TestBusiness::_()->cache_menu();
        echo json_encode($ret,JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        echo "\n".DATE(DATE_ATOM)."\n";
    }
}