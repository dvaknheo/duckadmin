<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Api;
use DuckPhp\SingletonEx\SingletonExTrait;

use DuckAdmin\System\AdminAction;
/**
 * 你的 Contoller 控制器调用这里的静态方法类。
 * 如果没法满足你的需求，那么请深入相关类
 */
class DuckAdminAction
{
    use SingletonExTrait;
	public function getCurrentAdminId()
	{
		//
	}
	public function isSuperAdmin($admin_id)
	{
		//
	}
	public function hasPermission()
	{
		//
	}
    /**
     * 检查权限 你的方法调用这个杜绝外部访问。
     */
    public function checkPermission()
    {
        //return AdminAction::G()->doCheckPermission();
    }
}