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
		$sql="select role_id from wa_admin_roles where id = ?";
		$data = static::Db()->fetchColumn($sql,$admin_id);
    }
	public function addFirstRole($admin)
	{
        $smt = $pdo->prepare("insert into `wa_admin_roles` (`role_id`, `admin_id`) values (:role_id, :admin_id)");
        $smt->bindValue('role_id', 1);
        $smt->bindValue('admin_id', $admin_id);
        $smt->execute();
	}
}