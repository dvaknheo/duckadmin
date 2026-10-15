<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */
namespace DuckAdmin\Admin_ThinkPHPModel\System;

use DuckAdmin\Admin\System\AdminApp;
use DuckAdmin\Ext\ThinkPHPOrmExt;
use DuckPhp\Component\GlobalEvent;

class ThinkPHPModelApp extends AdminApp
{
    public function __construct()
    {
        parent::__construct();
        $this->options['name'] = 'Admin_ThinkPHPModel';
        $this->options['ext'][ThinkPHPOrmExt::class] = true;

    }
    public function onInit(): void
    {
        parent::onInit();
        GlobalEvent::_()->on('installing', [self::class, 'onInstalling']);
        \DuckAdmin\Admin\Model\AdminModel::_(new \DuckAdmin\Admin_ThinkPHPModel\Overrided\Model\Adapter\AdminModelAdapter());
        \DuckAdmin\Admin\Model\RoleModel::_(new \DuckAdmin\Admin_ThinkPHPModel\Overrided\Model\Adapter\RoleModelAdapter());
        \DuckAdmin\Admin\Model\PermissionModel::_(new \DuckAdmin\Admin_ThinkPHPModel\Overrided\Model\Adapter\PermissionModelAdapter());
        \DuckAdmin\Admin\Model\RoleUserModel::_(new \DuckAdmin\Admin_ThinkPHPModel\Overrided\Model\Adapter\RoleUserModelAdapter());
        \DuckAdmin\Admin\Model\RolePermissionModel::_(new \DuckAdmin\Admin_ThinkPHPModel\Overrided\Model\Adapter\RolePermissionModelAdapter());
    }
    public static function onInstalling(): void
    {
        ThinkPHPOrmExt::_()->bootThinkORM(); 
    }

}