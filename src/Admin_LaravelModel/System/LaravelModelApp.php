<?php declare(strict_types=1);

namespace DuckAdmin\Admin_LaravelModel\System;

use DuckAdmin\System\LaravelOrmExt;
use DuckAdmin\Admin\System\AdminApp;
use DuckPhp\Component\GlobalEvent;

class LaravelModelApp extends AdminApp
{
    public function __construct()
    {
        parent::__construct();
        $this->options['name'] = 'Admin_LaravelModel';
    }
    public function onInit(): void
    {
        parent::onInit();
        GlobalEvent::_()->on('installing', [self::class, 'onInstalling']);
        LaravelOrmExt::_()->init([]);
        \DuckAdmin\Admin\Model\AdminModel::_(new \DuckAdmin\Admin_LaravelModel\Overrided\Model\Adapter\AdminModelAdapter());
        \DuckAdmin\Admin\Model\RoleModel::_(new \DuckAdmin\Admin_LaravelModel\Overrided\Model\Adapter\RoleModelAdapter());
        \DuckAdmin\Admin\Model\PermissionModel::_(new \DuckAdmin\Admin_LaravelModel\Overrided\Model\Adapter\PermissionModelAdapter());
        \DuckAdmin\Admin\Model\RoleUserModel::_(new \DuckAdmin\Admin_LaravelModel\Overrided\Model\Adapter\RoleUserModelAdapter());
        \DuckAdmin\Admin\Model\RolePermissionModel::_(new \DuckAdmin\Admin_LaravelModel\Overrided\Model\Adapter\RolePermissionModelAdapter());
    }
    public static function onInstalling(): void
    {
        LaravelOrmExt::_()->bootEloquent(); 
    }
}