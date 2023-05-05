<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Model;
/**
 * 菜单模型
 */
class AdminRoleModel extends BaseModel
{
    public function getRoles($admin_id)
    {
		return AdminRole::where('admin_id', $admin_id)->pluck('role_id');
    }
}