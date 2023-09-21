<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\App;

use DuckPhp\SingletonEx\SingletonExTrait;
/**
 * 这里是给外部调用的 服务类，一般是用于
 * 这里调用各种业务服务，你的 Business 业务层调用这里的静态方法
 * 当然，如果你认为已经没法满足你了，修改 Business 的实现也行
 */
class DuckAdminService
{
    use SingletonExTrait;
    /**
     * 管理员登录
     */
    public static function AdminLogin()
    {
    }
    /*
     * 是否是超级管理员
     */
    public static function IsSuperAdmin($admin)
    {
    }
    
}