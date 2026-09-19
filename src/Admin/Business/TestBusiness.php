<?php declare(strict_types=1);
/**
 * DuckPhp Admin System - Setting Business
 * 系统设置业务（超管用）
 */
namespace DuckAdmin\Admin\Business;

use DuckAdmin\Admin\Model\PermissionModel;
use DuckPhp\Ext\PermissionMenu;

class TestBusiness extends Base
{
    public function rebuild_menu()
    {
        PermissionMenu::_()->buildAndSaveToConfigJsonFile();
        PermissionModel::_()->clean();
        $menuTree = PermissionMenu::_()->loadAll();

        PermissionModel::_()->importMenu($menuTree);
        return $menuTree;
    }
    public function cache_menu()
    {
        return PermissionMenu::_()->buildAndSaveToConfigJsonFile();
    }
    public function test()
    {
        return 'test';
    }

}
