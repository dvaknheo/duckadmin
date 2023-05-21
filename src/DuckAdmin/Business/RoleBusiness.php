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
        $role_ids = Auth::getScopeRoleIds(true);
        if (!$id) {
            $where['id'] = ['in', $role_ids];
        } elseif (!in_array($id, $role_ids)) {
            throw new BusinessException('无权限');
        }
        $query = $this->doSelect($where, $field, $order);
        return $this->doFormat($query, $format, $limit);
	}
	public function insertRole()
	{
		$pid = $data['pid'] ?? null;
		if (!$pid) {
			return $this->json(1, '请选择父级角色组');
		}
		if (!Auth::isSupperAdmin() && !in_array($pid, Auth::getScopeRoleIds(true))) {
			return $this->json(1, '父级角色组超出权限范围');
		}
		$this->checkRules($pid, $data['rules'] ?? '');

		$id = $this->doInsert($data);
	}
	public function updateRole()
	{
		[$id, $data] = $this->updateInput($request);
        $is_supper_admin = Auth::isSupperAdmin();
        $descendant_role_ids = Auth::getScopeRoleIds();
        if (!$is_supper_admin && !in_array($id, $descendant_role_ids)) {
            return $this->json(1, '无数据权限');
        }

        $role = Role::find($id);
        if (!$role) {
            return $this->json(1, '数据不存在');
        }
        $is_supper_role = $role->rules === '*';

        // 超级角色组不允许更改rules pid 字段
        if ($is_supper_role) {
            unset($data['rules'], $data['pid']);
        }

        if (key_exists('pid', $data)) {
            $pid = $data['pid'];
            if (!$pid) {
                return $this->json(1, '请选择父级角色组');
            }
            if ($pid == $id) {
                return $this->json(1, '父级不能是自己');
            }
            if (!$is_supper_admin && !in_array($pid, Auth::getScopeRoleIds(true))) {
                return $this->json(1, '父级超出权限范围');
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
        if (in_array(1, $ids)) {
            return $this->json(1, '无法删除超级管理员角色');
        }
        if (!Auth::isSupperAdmin() && array_diff($ids, Auth::getScopeRoleIds())) {
            return $this->json(1, '无删除权限');
        }
        $tree = new Tree(Role::get());
        $descendants = $tree->getDescendant($ids);
        if ($descendants) {
            $ids = array_merge($ids, array_column($descendants, 'id'));
        }
        $this->doDelete($ids);
	}
	public function tree()
	{
        if (empty($role_id)) {
            return $this->json(0, 'ok', []);
        }
        if (!Auth::isSupperAdmin() && !in_array($role_id, Auth::getScopeRoleIds(true))) {
            return $this->json(1, '角色组超出权限范围');
        }
        $rule_id_string = Role::where('id', $role_id)->value('rules');
        if ($rule_id_string === '') {
            return $this->json(0, 'ok', []);
        }
        $rules = Rule::get();
        $include = [];
        if ($rule_id_string !== '*') {
            $include = explode(',', $rule_id_string);
        }
        $items = [];
        foreach ($rules as $item) {
            $items[] = [
                'name' => $item->title ?? $item->name ?? $item->id,
                'value' => (string)$item->id,
                'id' => $item->id,
                'pid' => $item->pid,
            ];
        }
        $tree = new Tree($items);
		return $tree;
	}
}