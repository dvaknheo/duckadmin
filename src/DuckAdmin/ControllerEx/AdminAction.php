<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\ControllerEx;

use DuckAdmin\Business\AdminBusiness;
use DuckAdmin\ControllerEx\AdminSession;
use DuckAdmin\System\ProjectController;

class AdminAction extends ProjectController
{
    public function __construct()
    {
    }
    public function doCheckPermission()
    {
        $admin = AdminSession::G()->getCurrentAdmin();
        $path_info = static::getPathInfo();
        $flag = AdminBusiness::G()->checkPermission($admin,$path_info);
        return $flag;
    }
    // 这里是视图相关的
    protected function initViewData()
    {
        // 这两个重复调用，性能可以忽略不记。
        $admin = AdminSession::G()->getCurrentAdmin();
        $path_info = App::getPathInfo();
        
        $menu = AdminBusiness::G()->getMenu($admin['id'],$path_info);
        
        static::assignViewData('menu', $menu);
        static::assignViewData('admin', $admin);
        
        static::setViewHeadFoot('header','footer');
        // 页眉页脚，如果是其他项目引用的时候，该怎么处理的问题。
    }
}