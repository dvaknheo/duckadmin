<?php
namespace DuckAdmin\Business;

use DuckAdmin\Model\RuleModel;
use DuckAdmin\Model\RoleModel;

/**
 * 个人资料业务
 */
class RoleBusiness extends BaseBusiness 
{
	public function selectRoles($id)
	{
		return [['name' => '超级管理员','value'=>1]];
		//return 
        [$where, $format, $limit, $field, $order] = $this->selectInput($request);
        $role_ids = Auth::getScopeRoleIds(true);  //------------>TODO
        if (!$id) {
            $where['id'] = ['in', $role_ids];
        } else{
			static::ThrowOn(!in_array($id, $role_ids),'无权限',1);
        }
        $query = $this->doSelect($where, $field, $order);
        return $this->doFormat($query, $format, $limit);
	}
	public function insertRole($data)
	{
		$pid = $data['pid'] ?? null;
		static::ThrowOn(!$pid,'请选择父级角色组',1);
		
		if (!Auth::isSupperAdmin() && !in_array($pid, Auth::getScopeRoleIds(true))) {
			static::ThrowOn(true,'父级角色组超出权限范围',1);
		}
		$this->checkRules($pid, $data['rules'] ?? '');

		$id = $this->doInsert($data);
	}
	public function updateRole()
	{
		[$id, $data] = $this->updateInput($request);
       ;
        $descendant_role_ids = Auth::getScopeRoleIds();
        if (!Auth::isSupperAdmin() && !in_array($id, Auth::getScopeRoleIds())) {
			static::ThrowOn(true,'无数据权限',1);
        }

        $role = Role::find($id);
		static::ThrowOn(!$role,'数据不存在',1);

        $is_supper_role = $role['rules'] === '*';

        // 超级角色组不允许更改rules pid 字段
        if ($is_supper_role) {
            unset($data['rules'], $data['pid']);
        }

        if (key_exists('pid', $data)) {
            $pid = $data['pid'];
			static::ThrowOn(!$pid,'请选择父级角色组',1);
			static::ThrowOn($pid == $id,'父级不能是自己',1);
            if (!Auth::isSupperAdmin() && !in_array($pid, Auth::getScopeRoleIds(true))) {
				static::ThrowOn(true,'父级超出权限范围',1);
            }
        } else {
            $pid = $role->pid;
        }

        if (!$is_supper_role) {
            $this->checkRules($pid, $data['rules'] ?? '');
        }

        $this->doUpdate($id, $data);

        // 删除所有子角色组中已经不存在的权限
        if (!$is_supper_role) {
            $tree = new Tree(Role::select(['id', 'pid'])->get());
            $descendant_roles = $tree->getDescendant([$id]);
            $descendant_role_ids = array_column($descendant_roles, 'id');
            $rule_ids = $data['rules'] ? explode(',', $data['rules']) : [];
            foreach ($descendant_role_ids as $role_id) {
                $tmp_role = Role::find($role_id);
                $tmp_rule_ids = $role->getRuleIds();
                $tmp_rule_ids = array_intersect($rule_ids, $tmp_rule_ids);
                $tmp_role->rules = implode(',', $tmp_rule_ids);
                $tmp_role->save();
            }
        }
	}
	public function deleteRole()
	{
		$ids = $this->deleteInput($request);
        
        static::ThrowOn(in_array(1, $ids), '无法删除超级管理员角色');
        
        if (!Auth::isSupperAdmin() && array_diff($ids, Auth::getScopeRoleIds())) {
			static::ThrowOn(true,'无删除权限',1);
        }
		
        $tree = new Tree(Role::get());
        $descendants = $tree->getDescendant($ids);
        if ($descendants) {
            $ids = array_merge($ids, array_column($descendants, 'id'));
        }
        $this->doDelete($ids);
	}
	public function tree($role_id)
	{
        if (empty($role_id)) {
            return [];
        }
        if (!$this->isSupperAdmin() && !in_array($role_id, $this->getScopeRoleIds(true))) {
            static::ThrowOn(true,'角色组超出权限范围',1);
        }
		
        $rule_id_string = Role::where('id', $role_id)->value('rules');
        if ($rule_id_string === '') {
            return [];
        }
        
		$include =($rule_id_string !== '*') ? explode(',', $rule_id_string) : [];
        
		
        $items = RuleModel::G()->allRulesForTree();		
        $tree = new Tree($items);
        return [$tree->getTree($include)];
	}
	
	
	
    /**
     * 是否是超级管理员
     * @param int $admin_id
     * @return bool
     */
    public static function isSupperAdmin(int $admin_id = 0): bool
    {
        if (!$admin_id) {
            if (!$roles = admin('roles')) {
                return false;
            }
        } else {
            $roles = AdminRole::where('admin_id', $admin_id)->pluck('role_id');
        }
        $rules = Role::whereIn('id', $roles)->pluck('rules');
        return $rules && in_array('*', $rules->toArray());
    }
	/**
     * 获取权限范围内的所有角色id
     * @param bool $with_self
     * @return array
     */
    public static function getScopeRoleIds(bool $with_self = false): array
    {
		//!Auth::isSupperAdmin() && !in_array($role_id, $this->getScopeRoleIds(true)
        if (!$admin = admin()) {
            return [];
        }
        $role_ids = $admin['roles'];
        $rules = Role::whereIn('id', $role_ids)->pluck('rules')->toArray();
        if ($rules && in_array('*', $rules)) {
            return Role::pluck('id')->toArray();
        }

        $roles = Role::get();
        $tree = new Tree($roles);
        $descendants = $tree->getDescendant($role_ids, $with_self);
        return array_column($descendants, 'id');
    }
}