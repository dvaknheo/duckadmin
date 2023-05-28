<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Model;
/**
 * 菜单模型
 */
class RoleModel extends BaseModel
{
	public function getRules($roles)
	{
		$sql="select rules from wa_roles where id in (". static::Db()->quoteIn($roles).')';
		$data = static::Db()->fetchAll($sql);
		return array_column($data,'rules');
	}
	public function hasSuperAdmin($roles)
    {
		$sql="select rules from wa_roles where id in (" . static::Db()->quoteIn($roles).')';
		$rules = static::Db()->fetchColumn($sql);
		
        $rule_ids = [];
        foreach ($rules as $rule_string) {
            if (!$rule_string) {
                continue;
            }
            $rule_ids = array_merge($rule_ids, explode(',', $rule_string));
        }
		return $rule_ids;
	}
	
	public function getAllId()
	{
		$sql="select id from wa_roles";
		$data = static::Db()->fetchAll($sql);
		return array_column($data,'id');
	}
	public function getAll()
	{
		$sql="select * from wa_roles";
		$data = static::Db()->fetchAll($sql);
		return $data;
	}
	public function getById($id)
	{
		$sql="select * from wa_roles where id = ?";
		$data = static::Db()->fetch($sql,$id);
		return $data;
	}
	public function getRulesByRoleId($role_id)
	{
		$sql = "select rules from wa_roles where id = ?";
		$data = static::Db()->fetchColumn($sql,$role_id);
		return $data;
	}
	public function getAllIdPid()
	{
		$sql = "select id,pid from wa_roles";
		$data = static::Db()->fetchAll($sql);
		return $data;
	}
	public function deleteByIds($ids)
	{
		$sql="delete from wa_roles where id in (" . static::Db()->quoteIn($ids).')';
		static::Db()->execute($sql);
	}
	public function addRole($data)
	{
		$time = date('Y-m-d H:i:s');
		$data['created_at']=$time;
		$data['updated_at']=$time;
		static::Db()->insertData('wa_roles',$data);
		
		return static::Db()->lastInsertId();
	}
	public function updateRole($id, $data)
	{
		$time = date('Y-m-d H:i:s');
		$data['updated_at'] = $time;
		return static::Db()->updateData("wa_roles", $id, $data, 'id');
	}
	public function updateRoleX($role_id,$rule_ids)
	{
		$data = static::Db()->fetch("select * from wa_roles where id = ? ",$role_id);
		$data['rules'] = implode(',', array_intersect(explode(',',$data['rules']),$rule_ids));
		$time = date('Y-m-d H:i:s');
		$data['updated_at'] = $time;
		return static::Db()->updateData("wa_roles", $id, $data, 'id');
	}

}