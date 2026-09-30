<?php declare(strict_types=1);

namespace DuckAdmin\Admin_LaravelModel\System;

use LaravelExp\LaravelModel\Adapter\AdminModelAdapter as LaravelAdminModelAdapter;
use LaravelExp\LaravelModel\Adapter\PermissionModelAdapter as LaravelPermissionModelAdapter;
use LaravelExp\LaravelModel\Adapter\RoleModelAdapter as LaravelRoleModelAdapter;
use LaravelExp\LaravelModel\Adapter\RolePermissionModelAdapter as LaravelRolePermissionModelAdapter;
use LaravelExp\LaravelModel\Adapter\RoleUserModelAdapter as LaravelRoleUserModelAdapter;

use DuckAdmin\System\ThinkPhpOrmExt;
use DuckPhp\DuckPhp;

class LaravelModelApp extends DuckPhp
{
    public $options = [
        'ext' => [
            'laravel_model' => [
                'overrider_callback' => null,
            ],
        ],
    ];
    public static function override()
    {
        \DuckAdmin\Admin\Model\AdminModel::_(new \LaravelExp\LaravelModel\Adapter\AdminModelAdapter());
        \DuckAdmin\Admin\Model\RoleModel::_(new \LaravelExp\LaravelModel\Adapter\RoleModelAdapter());
        \DuckAdmin\Admin\Model\PermissionModel::_(new \LaravelExp\LaravelModel\Adapter\PermissionModelAdapter());
        \DuckAdmin\Admin\Model\RoleUserModel::_(new \LaravelExp\LaravelModel\Adapter\RoleUserModelAdapter());
        \DuckAdmin\Admin\Model\RolePermissionModel::_(new \LaravelExp\LaravelModel\Adapter\RolePermissionModelAdapter());
    }

}