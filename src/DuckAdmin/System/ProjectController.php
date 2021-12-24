<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\System;

use DuckPhp\Foundation\SimpleControllerTrait;
use DuckPhp\Helper\ControllerHelperTrait;

use DuckAdmin\ControllerEx\AdminSession;
use DuckAdmin\ControllerEx\AdminAction;
use DuckAdmin\Business\AdminBusiness;

/**
 * 这是充当 Helper 助手的 控制器基类
 * 这里要注意的是，控制器的公开动态方法都会当成 web 动作，所以尽量避免公开动态方法
 */
class ProjectController
{
    use SimpleControllerTrait;
    use ControllerHelperTrait;
    public static function _GetInitRedirectRoute()
    {
        return static::Url('index?r=' . static::getPathInfo());
    }
    protected function initController()
    {
        // 入口类
        static::assignExceptionHandler(\Exception::class, function(){
            // 这里应该调整成可调的
            static::ExitRouteTo(static::_GetInitRedirectRoute());
        });        
        $flag = AdminAction::G()->doCheckPermission();
        
        if(!$flag){
            static::Exit404();
            return;
        }
        // 初始化View
        $this->initViewData();
    }
    // 这里是视图相关的
    protected function initViewData()
    {
        // 这两个重复调用，性能可以忽略不记。
        $admin = AdminSession::G()->getCurrentAdmin();
        $path_info = static::PathInfo();
        
        $menu = AdminBusiness::G()->getMenu($admin['id'],$path_info);
        
        static::assignViewData('menu', $menu);
        static::assignViewData('admin', $admin);
        
        static::setViewHeadFoot('header','footer');
        // 页眉页脚，如果是其他项目引用的时候，该怎么处理的问题。
    }
}
