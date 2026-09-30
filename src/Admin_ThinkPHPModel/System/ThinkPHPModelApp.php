<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */
namespace DuckAdmin\System;

use DuckAdmin\Admin_ThinkPHPModel\Overrided\Model\Adapter\AdminModelAdapter as ThinkPHPAdminModelAdapter;
use DuckAdmin\Admin_ThinkPHPModel\Overrided\Model\Adapter\PermissionModelAdapter as ThinkPHPPermissionModelAdapter;
use DuckAdmin\Admin_ThinkPHPModel\Overrided\Model\Adapter\RoleModelAdapter as ThinkPHPRoleModelAdapter;
use DuckAdmin\Admin_ThinkPHPModel\Overrided\Model\Adapter\RolePermissionModelAdapter as ThinkPHPRolePermissionModelAdapter;
use DuckAdmin\Admin_ThinkPHPModel\Overrided\Model\Adapter\RoleUserModelAdapter as ThinkPHPRoleUserModelAdapter;

use DuckPhp\DuckPhp;

class ThinkPHPModelApp extends DuckPhp
{
    public $options = [
        'ext' => [
            'thinkphp_model' => [
                'overrider_callback' => null,
            ],
        ],
    ];
    protected function doOverride()
    {
        \DuckAdmin\Admin\Model\AdminModel::_(new ThinkPHPAdminModelAdapter());
        \DuckAdmin\Admin\Model\RoleModel::_(new ThinkPHPRoleModelAdapter());
        \DuckAdmin\Admin\Model\PermissionModel::_(new ThinkPHPPermissionModelAdapter());
        \DuckAdmin\Admin\Model\RoleUserModel::_(new ThinkPHPRoleUserModelAdapter());
        \DuckAdmin\Admin\Model\RolePermissionModel::_(new ThinkPHPRolePermissionModelAdapter());
    }
}