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
        
        [$items,$total] = AdminModel::_()->doSelect($where, $field, $order,$page,$limit);
        
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
        $role_ids=$input['roles'];
        $role_ids = $role_ids ? explode(',', $role_ids) : [];
        Helper::BusinessThrowOn(!$role_ids,'至少选择一个角色组',1);
        
        // 这里两个
        $flag = CommonService::_()->noRole($op_id,$role_ids, false);
        Helper::BusinessThrowOn($flag,'角色超出权限范围',1);
        
        //$is_supper_admin = $this->isSupperAdmin($op_id);
        // 这里要调整权限
        if (false &&!Auth::isSupperAdmin() && $this->dataLimit) {
            if (!empty($data[$this->dataLimitField])) {
                $admin_id = $data[$this->dataLimitField];
                if (!in_array($admin_id, Auth::getScopeAdminIds(true))) {
                    throw new BusinessException('无数据权限');
                }
            }
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
        
        if (isset($data['status']) && $data['status'] == 1 && $admin_id === $op_id) {
            Helper::BusinessThrowOn(true,'不能禁用自己',1);
        }

        // 需要更新角色
        if ($role_ids !== null) {
            Helper::BusinessThrowOn(!$role_ids,'至少选择一个角色组',1);
            
            $role_ids = explode(',', $role_ids);
            $exist_role_ids = AdminRoleModel::_()->rolesByAdmin($admin_id);
            $scope_role_ids = AdminRoleModel::_()->rolesByAdmin($op_id);
            
            $is_supper_admin = CommonService::_()->isSupperAdmin($op_id);
            if (!$is_supper_admin){
                if(!array_intersect($exist_role_ids, $scope_role_ids)) {
                    Helper::BusinessThrowOn(true,'无权限更改该记录',1);
                }
                if (array_diff($role_ids, $scope_role_ids)) {
                    Helper::BusinessThrowOn(true,'角色超出权限范围',1);
                }
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
            $scope_role_ids = AdminRoleModel::_()->rolesByAdmin($op_id);
            if (array_diff($ids, $scope_role_ids)) {
                Helper::BusinessThrowOn(true,'无数据权限',1);
            }
        }
        
        AdminModel::_()->deleteByIds($ids);
        AdminRoleModel::_()->deleteByAdminIds($ids);
        
        return true;
    }
}