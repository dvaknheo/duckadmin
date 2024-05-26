<?php
namespace DuckAdmin\Business;

use DuckAdmin\Model\AdminModel;
use DuckAdmin\Model\RoleModel;
use DuckAdmin\Model\AdminRoleModel;
/**
 * 管理员业务
 */
class AdminBusiness extends Base
{
    public function showAdmins($op_id,$input)
    {
        [$where, $format, $limit, $field, $order, $page] = AdminModel::_()->selectInput($input);
        
        $op_role_ids = AdminRoleModel::_()->rolesByAdminId($op_id);
        $is_super = RoleModel::_()->hasSuper($op_role_ids);
        if(!$is_super){
            $with_self = false;
            
            $tree = new Tree(RoleModel::_()->getAll());
            $descendants = $tree->getDescendant($op_role_ids, $with_self);
            $child_role_ids = array_column($descendants, 'id');
            $child_admin_ids = AdminRoleModel::_()->adminIdByRoles($child_role_ids);
            $where['id'] =['in',$child_admin_ids ];  // 限定当前role ，或者该改成子查询
        }
        [$items,$total] = AdminModel::_()->doSelect($where, $field, $order,$page,$limit);
        
        
        // 后处理
        if ('select' ===  ($input['format']??null)) {
            return CommonService::_()->formatSelect($items,$format,$limit);
        }
        
        // 后处理
        $roles_map = AdminRoleModel::_()->getAdminRoles(array_column($items, 'id'));
        foreach ($items as $index => $item) {
            $admin_id = $item['id'];
            $items[$index]['roles'] = isset($roles_map[$admin_id]) ? implode(',', $roles_map[$admin_id]) : '';
            $items[$index]['show_toolbar'] = $admin_id != $op_id;
        }
        return [$items,$total];
    }
    public function addAdmin($op_id, $input)
    {
        $role_ids = $input['roles'];
        $role_ids = $role_ids ? explode(',', $role_ids) : [];
        Helper::BusinessThrowOn(!$role_ids,'至少选择一个角色组',1);
        
        // 这里两个
        $flag = CommonService::_()->noRole($op_id,$role_ids, false);
        Helper::BusinessThrowOn($flag,'角色超出权限范围',1);
        
        $is_supper_admin = CommonService::_()->isSupperAdmin($op_id);
        if (!$is_supper_admin){
            $scope_role_ids = AdminRoleModel::_()->rolesByAdminId($op_id);
            $ext_roles = array_diff($role_ids, $scope_role_ids);
            Helper::BusinessThrowOn(!empty($ext_roles),'只能改操作者下属的角色'.implode(',',$ext_roles),1);
            
        }
        $data = AdminModel::_()->inputFilter($input);
        $admin_id = AdminModel::_()->addAdmin($data);
        AdminRoleModel::_()->renew($admin_id,$role_ids);
        
        return $admin_id;
    }
    public function updateAdmin($op_id,$input)
    {
        $admin_id = $input['id'];
        $role_ids = $input['roles'];
        $data = AdminModel::_()->inputFilter($input);
        Helper::BusinessThrowOn(!$admin_id,'缺少参数',1);
        
        $flag = (isset($data['status']) && $data['status'] == 1 && $admin_id === $op_id);
        Helper::BusinessThrowOn($flag,'不能禁用自己',1);

        // 需要更新角色
        if ($role_ids !== null) {
            Helper::BusinessThrowOn(!$role_ids,'至少选择一个角色组',1);
            
            $role_ids = explode(',', $role_ids);
            $exist_role_ids = AdminRoleModel::_()->rolesByAdminId($admin_id);
            
            $is_supper_admin = CommonService::_()->isSupperAdmin($op_id);
            if (!$is_supper_admin){
                $scope_role_ids = AdminRoleModel::_()->rolesByAdminId($op_id); //getScopeRoleIds($op_id,false);
                $same_roles = array_intersect($exist_role_ids, $scope_role_ids);
                Helper::BusinessThrowOn(!$same_roles,'被操作者和操作者有不同角色',1);
                $ext_roles = array_diff($role_ids, $scope_role_ids);
                Helper::BusinessThrowOn($ext_roles,'只能改操作者下属的角色',1);
                
            }
            AdminRoleModel::_()->updateAdminRole($admin_id, $exist_role_ids, $role_ids);
        }
        AdminModel::_()->updateAdmin($admin_id, $data);

        return;
    }
    public function deleteAdmin($op_id, $ids)
    {
        if (!$ids) {
            return true;
        }
        $ids = (array)$ids;
        Helper::BusinessThrowOn(in_array($op_id, $ids),'不能删除自己',1);
        $is_supper_admin = CommonService::_()->isSupperAdmin($op_id);
        if (!$is_supper_admin){
            $scope_role_ids = AdminRoleModel::_()->rolesByAdminId($op_id);
            $ext_roles = array_diff($ids, $scope_role_ids);
            Helper::BusinessThrowOn($ext_roles,'无数据权限',1);
        }
        
        AdminModel::_()->deleteByIds($ids);
        AdminRoleModel::_()->deleteByAdminIds($ids);
        
        return true;
    }
}