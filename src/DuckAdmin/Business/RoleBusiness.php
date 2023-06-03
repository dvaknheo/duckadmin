<?php
namespace DuckAdmin\Business;

use DuckAdmin\Model\RuleModel;
use DuckAdmin\Model\RoleModel;
use DuckAdmin\Model\AdminRoleModel;

/**
 * 个人资料业务
 */
class RoleBusiness extends BaseBusiness 
{
	public function selectRoles($op_id,$id,$input)
	{
		//这里折腾好久
		//return [['name' => '超级管理员','value'=>1]];
		//selectInput 要改
		file_put_contents(__DIR__.'/x.log',microtime(true) ."\n",FILE_APPEND);
        [$where, $format, $limit, $field, $order] = $this->selectInput($input, RoleModel::G()->table(), null, null);
		$my_ids = AdminRoleModel::G()->getRoles($op_id);
        $role_ids = $this->getScopeRoleIds($my_ids, true);
        if (!$id) {
            $where['id'] = ['in', $role_ids];
        } else{
			static::ThrowOn(!in_array($id, $role_ids),'无权限',1);
        }
		[$data,$total]  = RoleModel::G()->doSelect($where, $field, $order,1,$limit);
        $data = $this->doFormat($data, $total, $format, $limit);
		file_put_contents(__DIR__.'/x.log',microtime(true) ."\n",FILE_APPEND);
		return $data;
		
	}
	public function insertRole($op_id, $data)
	{
		$pid = $data['pid'] ?? null;
		static::ThrowOn(!$pid,'请选择父级角色组',1);
		if ($this->noRole($op_id, $pid, true)) {
			static::ThrowOn(true,'父级角色组超出权限范围',1);
		}

		$this->checkRulesInput($pid, $data['rules'] ?? '');

		$id = RoleModel::G()->addRole($data);
		return $id;
	}
	public function updateRole($op_id, $input)
	{
		[$role_id, $data] = $this->updateInput($input); // 改这里
       
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
            $this->checkRulesInput($pid, $data['rules'] ?? '');
        }


        RoleModel::G()->updateRole($id, $data);

        // 删除所有子角色组中已经不存在的权限
        if (!$is_supper_role) {
		
			// 这里，整合
			$treedata = RoleModel::G()->getAllIdPid();
            $tree = new Tree($treedata);
            $descendant_roles = $tree->getDescendant([$id]);
            $descendant_role_ids = array_column($descendant_roles, 'id');
			
			// 这里要调整？
            $rule_ids = $data['rules'] ? explode(',', $data['rules']) : [];
            foreach ($descendant_role_ids as $role_id) {
				RoleModel::G()->updateRoleX($role_id,$rule_ids);
            }
        }
	}
	
	public function deleteRole($op_id, $ids)
	{
		$ids=is_array($ids)?$ids:[$ids];
        static::ThrowOn(in_array(1, $ids), '无法删除超级管理员角色');
        
		$flag = $this->noRole($op_id,$ids);
		static::ThrowOn($flag ,'无删除权限',1);
        
        $tree = new Tree(RoleModel::G()->getAll());
        $descendants = $tree->getDescendant($ids);
        if ($descendants) {
            $ids = array_merge($ids, array_column($descendants, 'id'));
        }

		RoleModel::G()->deleteByIds($ids);
	}
	
	public function tree($op_id, $role_id)
	{
        if (empty($role_id)) {
            return [];
        }
        $flag = $this->noRole($op_id, $role_id, true);
        static::ThrowOn($flag,'角色组超出权限范围',1);
        
		
        $rule_id_string = RoleModel::G()->getRulesByRoleId($role_id);
        if ($rule_id_string === '') {
            return [];
        }
        
		$include =($rule_id_string !== '*') ? explode(',', $rule_id_string) : [];
        
		
        $items = RuleModel::G()->allRulesForTree();		
        $tree = new Tree($items);
        return $tree->getTree($include);
	}
    /**
     * 检查权限字典是否合法
     * @param int $role_id
     * @param $rule_ids
     * @return void
     * @throws BusinessException
     */
    protected function checkRulesInput(int $role_id, $rule_ids)
    {
        if (!$rule_ids) {
			return;
		}
		$rule_ids = explode(',', $rule_ids);
		if (in_array('*', $rule_ids)) {
			static::ThrowOn(true, '非法数据');
		}
		$flag = RuleModel::G()->checkRulesExist($rule_ids);

		static::ThrowOn(!$flag, '权限不存在');

		$rule_id_string = RoleModel::G()->getRulesByRoleId($role_id);
		static::ThrowOn($rule_id_string === '', '数据超出权限范围');
		
		if ($rule_id_string === '*') {
			return;
		}
		
		$legal_rule_ids = explode(',', $rule_id_string);
		if (array_diff($rule_ids, $legal_rule_ids)) {
			static::ThrowOn(true, '数据超出权限范围');
		}
    }
	// 这里要放到 Service 里
	/**
     * 获取权限范围内的所有角色id
     * @param bool $with_self
     * @return array
     */
    public function getScopeRoleIds($role_ids,  bool $with_self = false): array
    {
        //$role_ids = $admin['roles'];
		
        $rules = RoleModel::G()->getRules($role_ids);
        if ($rules && in_array('*', $rules)) {
            return RoleModel::G()->getAllId();
        }
        $roles = RoleModel::G()->getAll();
		
        $tree = new Tree($roles);
        $descendants = $tree->getDescendant($role_ids, $with_self);
        return array_column($descendants, 'id');
    }
}