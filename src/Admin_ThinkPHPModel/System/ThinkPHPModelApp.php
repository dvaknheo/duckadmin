<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */
namespace DuckAdmin\Admin_ThinkPHPModel\System;

use DuckAdmin\Admin\System\AdminApp;
use DuckAdmin\System\ThinkPHPOrmExt;

class ThinkPHPModelApp extends AdminApp
{
    public function __construct()
    {
        parent::__construct();
        $this->options['name'] = 'Admin_ThinkPHPModel';
    }
    public function onInited(): void
    {
        parent::onInited();
        if (!($this->options['admin_provider_enable']?? false)) {
            return;
        }

        ThinkPHPOrmExt::_()->init([]);
        \DuckAdmin\Admin\Model\AdminModel::_(new \DuckAdmin\Admin_ThinkPHPModel\Overrided\Model\Adapter\AdminModelAdapter());
        \DuckAdmin\Admin\Model\RoleModel::_(new \DuckAdmin\Admin_ThinkPHPModel\Overrided\Model\Adapter\RoleModelAdapter());
        \DuckAdmin\Admin\Model\PermissionModel::_(new \DuckAdmin\Admin_ThinkPHPModel\Overrided\Model\Adapter\PermissionModelAdapter());
        \DuckAdmin\Admin\Model\RoleUserModel::_(new \DuckAdmin\Admin_ThinkPHPModel\Overrided\Model\Adapter\RoleUserModelAdapter());
        \DuckAdmin\Admin\Model\RolePermissionModel::_(new \DuckAdmin\Admin_ThinkPHPModel\Overrided\Model\Adapter\RolePermissionModelAdapter());
    }
}