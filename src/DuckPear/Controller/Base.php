<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckPear\Controller;

class Base
{
    public function __construct()
    {
        $this->initController();
    }
    public static function _GetInitRedirectRoute()
    {
        return __url('index?r=' . static::PathInfo());
    }
    protected function initController()
    {
        // 入口类
        static::assignExceptionHandler(\Exception::class, function(){
            // 这里应该调整成可调的
            static::Show302(static::_GetInitRedirectRoute());
        });        
        $flag = AdminAction::G()->doCheckPermission();
        
        if(!$flag){
            static::Show404();
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
