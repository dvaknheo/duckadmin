<?php
namespace DuckAdmin\Business;

use DuckAdmin\Model\RuleModel;
use DuckAdmin\Model\RoleModel;
use DuckAdmin\Model\AdminRoleModel;

/**
 * 个人资料业务
 */
class RoleBusiness extends Base
{
    public function selectRoles($op_id,$id,$input)
    {
        //这里折腾好久
        //return [['name' => '超级管理员','value'=>1]];
        //file_put_contents(__DIR__.'/x.log',microtime(true) ."\n",FILE_APPEND);
        [$where, $format, $limit, $field, $order] = RoleModel::_()->selectInput($input);
        $my_ids = AdminRoleModel::_()->getRoles($op_id);
        $role_ids = CommonService::_()->getScopeRoleIds($my_ids, true);
        if (!$id) {
            $where['id'] = ['in', $role_ids];
        } else{
            Helper::BusinessThrowOn(!in_array($id, $role_ids),'无权限',1);
        }
        [$data,$total]  = RoleModel::_()->doSelect($where, $field, $order,1,$limit);
        $data = CommonService::_()->doFormat($data, $total, $format, $limit);
        //file_put_contents(__DIR__.'/x.log',microtime(true) ."\n",FILE_APPEND);
        return $data;
        
    }
    public function insertRole($op_id, $data)
    {
        $pid = $data['pid'] ?? null;
        Helper::BusinessThrowOn(!$pid,'请选择父级角色组',1);
        if (CommonService::_()->noRole($op_id, $pid, true)) {
            Helper::BusinessThrowOn(true,'父级角色组超出权限范围',1);
        }

        $this->checkRulesInput($pid, $data['rules'] ?? '');

        $id = RoleModel::_()->addRole($data);
        return $id;
    }
    ////[[[[

    ////////]]]]
    public function updateRole($op_id, $input)
    {
        $role_id = $input['id'];
        $id = $role_id;
        $data = RoleModel::_()->inputFilter($input);
       
        if (CommonService::_()->noRole($op_id, $role_id, true)) {
            Helper::BusinessThrowOn(true,'无数据权限',1);
        }

        $role = RoleModel::_()->getById($id);
        Helper::BusinessThrowOn(!$role,'数据不存在',1);
        $role_rules = $role['rules'] ? explode(',', $role['rules']):[];
        $is_supper_role = $role['rules'] === '*';

        // 超级角色组不允许更改rules pid 字段
        if ($is_supper_role) {
            unset($data['rules'], $data['pid']);
        }

        if (key_exists('pid', $data)) {
            $pid = $data['pid'];
            Helper::BusinessThrowOn(!$pid,'请选择父级角色组',1);
            Helper::BusinessThrowOn($pid == $id,'父级不能是自己',1);
            if (CommonService::_()->noRole($op_id, $role_id, true)) {
                Helper::BusinessThrowOn(true,'父级超出权限范围',1);
            }
        } else {
            $pid = $role['pid'];
        }

        if (!$is_supper_role) {
            $this->checkRulesInput($pid, $data['rules'] ?? '');
        }


        RoleModel::_()->updateRole($id, $data);

        // 删除所有子角色组中已经不存在的权限
        if (!$is_supper_role) {
        
            // 这里，整合
            $treedata = RoleModel::_()->getAllIdPid();
            $tree = new Tree($treedata);
            $descendant_roles = $tree->getDescendant([$id]);
            $descendant_role_ids = array_column($descendant_roles, 'id');
            
            $rule_ids = $data['rules'] ? explode(',', $data['rules']) : [];
            RoleModel::_()->updateRoleMore($descendant_role_ids,$rule_ids);
        }
        return $id;
    }
    
    public function deleteRole($op_id, $ids)
    {
        $ids=is_array($ids)?$ids:[$ids];
        Helper::BusinessThrowOn(in_array(1, $ids), '无法删除超级管理员角色');
        
        $flag = CommonService::_()->noRole($op_id,$ids);
        Helper::BusinessThrowOn($flag ,'无删除权限',1);
        
        $tree = new Tree(RoleModel::_()->getAll());
        $descendants = $tree->getDescendant($ids);
        if ($descendants) {
            $ids = array_merge($ids, array_column($descendants, 'id'));
        }

        RoleModel::_()->deleteByIds($ids);
    }
    
    public function tree($op_id, $role_id)
    {
        if (empty($role_id)) {
            return [];
        }
        $flag = CommonService::_()->noRole($op_id, $role_id, true);
        Helper::BusinessThrowOn($flag,'角色组超出权限范围',1);
        
        
        $rule_id_string = RoleModel::_()->getRulesByRoleId($role_id);
        if ($rule_id_string === '') {
            return [];
        }
        
        $include =($rule_id_string !== '*') ? explode(',', $rule_id_string) : [];
        
        
        $items = RuleModel::_()->allRulesForTree();		
        $tree = new Tree($items);
        return $tree->getTree($include);
    }
    /**
     * 检查权限字典是否合法
     * @param int $role_id
     * @param $rule_ids
     */
    protected function checkRulesInput(int $role_id, $rule_ids)
    {
        if (!$rule_ids) {
            return;
        }
        $rule_ids = explode(',', $rule_ids);
        if (in_array('*', $rule_ids)) {
            Helper::BusinessThrowOn(true, '非法数据');
        }
        $flag = RuleModel::_()->checkRulesExist($rule_ids);

        Helper::BusinessThrowOn(!$flag, '权限不存在');

        $rule_id_string = RoleModel::_()->getRulesByRoleId($role_id);
        Helper::BusinessThrowOn($rule_id_string === '', '数据超出权限范围');
        
        if ($rule_id_string === '*') {
            return;
        }
        
        $legal_rule_ids = explode(',', $rule_id_string);
        if (array_diff($rule_ids, $legal_rule_ids)) {
            Helper::BusinessThrowOn(true, '数据超出权限范围');
        }
    }
}