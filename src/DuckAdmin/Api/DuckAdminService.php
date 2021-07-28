<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\App;

use DuckPhp\SingletonEx\SingletonExTrait;
/**
 * 这里调用各种业务服务，你的 Business 业务层调用这里的静态方法
 */
class DuckAdminService
{
    use SingletonExTrait;
    
    public static function AdminLogin()
    {
    }
    public static function IsSuperAdmin($admin)
    {
    }
    
}