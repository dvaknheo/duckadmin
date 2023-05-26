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
        $role_ids = $this->getScopeRoleIds(true);
        if (!$id) {
            $where['id'] = ['in', $role_ids];
        } else{
			static::ThrowOn(!in_array($id, $role_ids),'无权限',1);
        }
        $query = RoleModel::G()->doSelect($where, $field, $order);
        return $this->doFormat($query, $format, $limit);
	}
	public function insertRole($data)
	{
		$pid = $data['pid'] ?? null;
		static::ThrowOn(!$pid,'请选择父级角色组',1);
		
		if ($this->noRole($admin, $pid, true)) {
			static::ThrowOn(true,'父级角色组超出权限范围',1);
		}
		$this->checkRules($pid, $data['rules'] ?? '');

		$id = RoleModel::G()->addRole($data);
	}
	public function updateRole()
	{
		[$role_id, $data] = $this->updateInput($request);
       ;
        $descendant_role_ids = $this->getScopeRoleIds();
        if ($this->noRole($admin, $role_id, true)) {
			static::ThrowOn(true,'无数据权限',1);
        }

        $role = RoleModel::G()->getById($id);
		static::ThrowOn(!$role,'数据不存在',1);
		$role_rules = $role['rules'] ? explode(',', $role['rules']):[];
        $is_supper_role = $role['rules'] === '*';

        // 超级角色组不允许更改rules pid 字段
        if ($is_supper_role) {
            unset($data['rules'], $data['pid']);
        }

        if (key_exists('pid', $data)) {
            $pid = $data['pid'];
			static::ThrowOn(!$pid,'请选择父级角色组',1);
			static::ThrowOn($pid == $id,'父级不能是自己',1);
            if ($this->noRole($admin, $role_id, true)) {
				static::ThrowOn(true,'父级超出权限范围',1);
            }
        } else {
            $pid = $role['pid'];
        }

        if (!$is_supper_role) {
            $this->checkRules($pid, $data['rules'] ?? '');
        }


        RoleModel::G()->updateRole($id, $data);

        // 删除所有子角色组中已经不存在的权限
        if (!$is_supper_role) {
			$treedata = RoleModel::G()->getAllIdPid();
            $tree = new Tree($treedata);
            $descendant_roles = $tree->getDescendant([$id]);
            $descendant_role_ids = array_column($descendant_roles, 'id');
            $rule_ids = $data['rules'] ? explode(',', $data['rules']) : [];
            foreach ($descendant_role_ids as $role_id) {
				static::ThrowOn(true,'这里要整合到一个函数',1);
                $tmp_role = RoleModel::getById($role_id);
                $tmp_rule_ids = $role_rules;  ////////////这里要改 
                $tmp_rule_ids = array_intersect($rule_ids, $tmp_rule_ids);
                $tmp_role->rules = implode(',', $tmp_rule_ids);
                $tmp_role->save();
            }
        }
	}
	
	public function deleteRole($ids)
	{
		$ids = $this->deleteInput($request); 
        
        static::ThrowOn(in_array(1, $ids), '无法删除超级管理员角色');
        
        if (!$this->isSupperAdmin() && array_diff($ids, $this->getScopeRoleIds())) {
			static::ThrowOn(true,'无删除权限',1);
        }
		
        $tree = new Tree(RoleModel::G()->getAll());
        $descendants = $tree->getDescendant($ids);
        if ($descendants) {
            $ids = array_merge($ids, array_column($descendants, 'id'));
        }
		RoleModel::G()->deleteByIds($ids);
	}
	
	public function tree($role_id)
	{
        if (empty($role_id)) {
            return [];
        }
		$admin=$this->getCurrentAdmin();
		
        if ($this->noRole($admin, $role_id, true)) {
            static::ThrowOn(true,'角色组超出权限范围',1);
        }
		
        $rule_id_string = RoleModel::getRulesByRoleId($role_id);
        if ($rule_id_string === '') {
            return [];
        }
        
		$include =($rule_id_string !== '*') ? explode(',', $rule_id_string) : [];
        
		
        $items = RuleModel::G()->allRulesForTree();		
        $tree = new Tree($items);
        return [$tree->getTree($include)];
	}
    /**
     * 检查权限字典是否合法
     * @param int $role_id
     * @param $rule_ids
     * @return void
     * @throws BusinessException
     */
    protected function checkRules(int $role_id, $rule_ids)
    {
        if (!$rule_ids) {
			return;
		}
		$rule_ids = explode(',', $rule_ids);
		if (in_array('*', $rule_ids)) {
			static::ThrowOn(true, '非法数据');
		}
		
		$flag =RuleModel::G()->checkRulesExist($rule_ids);
		static::ThrowOn(!$flag, '权限不存在');
		
		$rule_id_string = RoleModel::getRulesByRoleId($role_id);
		static::ThrowOn($rule_id_string === '', '数据超出权限范围');
		
		if ($rule_id_string === '*') {
			return;
		}
		
		$legal_rule_ids = explode(',', $rule_id_string);
		if (array_diff($rule_ids, $legal_rule_ids)) {
			static::ThrowOn(true, '数据超出权限范围');
		}
    }	
}