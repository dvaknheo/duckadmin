<?php declare(strict_types=1);
/**
 * DuckPhp Admin System - Setting Business
 * 系统设置业务（超管用）
 */
namespace DuckAdmin\Admin\Business;

use DuckAdmin\Admin\Model\PermissionModel;

class TestBusiness extends Base
{
    public function rebuild_menu()
    {
        Helper::_()->permissionMenu()->buildAndSaveToConfigJsonFile();
        PermissionModel::_()->clean();
        $menuTree = Helper::_()->permissionMenu()->loadAll();

        PermissionModel::_()->importMenu($menuTree);
        return $menuTree;
    }
    public function cache_menu()
    {
        return Helper::_()->permissionMenu()->buildAndSaveToConfigJsonFile();
    }
    public function test()
    {
        return 'test';
    }

}
