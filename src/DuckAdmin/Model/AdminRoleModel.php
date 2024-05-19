<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Model;
/**
 * 菜单模型
 */
class AdminRoleModel extends Base
{
    protected $table_name = 'admin_roles';

    public function getRoles($admin_id)
    {
        $sql="select role_id from `'TABLE'` where admin_id = ?";
        $data = $this->fetchAll($sql,$admin_id);
        return array_column($data,'role_id');
    }
    public function addFirstRole($admin_id)
    {
        $sql = "insert into `'TABLE'` (`role_id`, `admin_id`) values (?,?)";
        $data = $this->execute($sql,1,$admin_id);
    }
    public function getAdminRoles($admin_ids)
    {
        $sql= "select * from `'TABLE'` where admin_id in (".static::Db()->quoteIn($admin_ids).")";
        $roles = $this->fetchAll($sql);
        $roles_map = [];
        foreach ($roles as $role) {
            $roles_map[$role['admin_id']][] = $role['role_id'];
        }
        return $roles_map;
    }
    public function renew($admin_id,$role_ids)
    {
        $sql = "delete from `'TABLE'` where admin_id = ?";
        $this->execute($sql,$admin_id);
        foreach ($role_ids as $id) {
            $sql = "insert into `'TABLE'` (`role_id`, `admin_id`) values (?,?)";
            $data = $this->execute($sql,1,$admin_id);
        }
    }
    public function deleteByAdminIds($ids)
    {
        $sql ="delete from `'TABLE'` where admin_id in(".static::Db()->quoteIn($ids).")";
        $this->execute($sql);
    }
    public function deleteByAdminId($admin_id,$delete_ids)
    {
        $sql ="delete from `'TABLE'` where admin_id = ? and  role_id in(".static::Db()->quoteIn($delete_ids).")";
        $this->execute($sql,$admin_id);
    }
    public function updateAdminRole($admin_id, $exist_role_ids, $role_ids)
    {
            // 删除账户角色
            $delete_ids = array_diff($exist_role_ids, $role_ids);
            $this->deleteByAdminId($admin_id,$delete_ids);
            // 添加账户角色
            $add_ids = array_diff($role_ids, $exist_role_ids);
            foreach ($add_ids as $role_id) {
                $sql = "insert into `'TABLE'` (`role_id`, `admin_id`) values (?,?)";
                $this->execute($sql,$role_id,$admin_id);
            }
    }
    public function rolesByAdmin($admin_id)
    {
        $sql="select role_id from `'TABLE'` where admin_id = ?";
        $data = $this->fetchAll($sql,$admin_id);
        return array_column($data,'role_id');
    }
}