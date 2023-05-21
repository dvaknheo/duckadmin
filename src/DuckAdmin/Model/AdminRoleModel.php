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
		$data = static::Db()->fetchAll($sql,$admin_id);
		return array_column($data,'role_id');
    }
	public function addFirstRole($admin_id)
	{
        $sql = "insert into `wa_admin_roles` (`role_id`, `admin_id`) values (?,?)";
		$data = static::Db()->execute($sql,1,$admin_id);
	}
	public function getAdminRoles($admin_ids)
	{
		$sql= "select * from `wa_admin_roles` where admin_id in (".static::Db()->quoteIn($admin_ids).")";
		$roles = static::Db()->fetchAll($sql);
        $roles_map = [];
        foreach ($roles as $role) {
            $roles_map[$role['admin_id']][] = $role['role_id'];
        }
		return $roles_map;
	}
}