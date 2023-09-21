<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckPear\ControllerEx;

use DuckPear\Business\AdminBusiness;
use DuckPear\ControllerEx\AdminSession;
use DuckPear\System\ProjectAction;

class AdminAction extends ProjectAction
{
    public function doCheckPermission()
    {
        $admin = AdminSession::G()->getCurrentAdmin();
        $path_info = static::PathInfo();
        $flag = AdminBusiness::G()->checkPermission($admin,$path_info);
        return $flag;
    }
    public function initViewData()
    {
        // 这两个重复调用，性能可以忽略不记。
        $admin = AdminSession::G()->getCurrentAdmin();
        $path_info = static::getPathInfo();
        
        $menu = AdminBusiness::G()->getMenu($admin['id'],$path_info);
        
        static::assignViewData('menu', $menu);
        static::assignViewData('admin', $admin);
        
        static::setViewHeadFoot('header','footer');
        // 页眉页脚，如果是其他项目引用的时候，该怎么处理的问题。
    }
}