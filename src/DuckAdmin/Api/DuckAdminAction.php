<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Api;
use DuckPhp\SingletonEx\SingletonExTrait;

use DuckAdmin\System\Controller;
/**
 * 你的 Contoller 控制器调用这里的静态方法类。
 */
class DuckAdminAction
{
    use SingletonExTrait;
    
    // 你的方法调用这个杜绝外部访问。
    public static function CheckPermission()
    {
        //return Controller::CheckPermission();
    }
    // 调用这个，询问当前是否是超级管理员
    public static function IsSuperAdmin()
    {
        //return Controller::IsSuperAdmin();
    }
}