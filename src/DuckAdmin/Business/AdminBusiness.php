<?php
namespace DuckAdmin\Business;

use DuckAdmin\Model\AdminModel;
use DuckAdmin\Model\RoleModel;
use DuckAdmin\Model\AdminRoleModel;
/**
 * 管理员业务
 */
class AdminBusiness extends BaseBusiness
{
	public function showAdmins($login_admin_id,$input, $table, $dataLimit=null, $dataLimitField =null)
	{
        [$where, $format, $limit, $field, $order] = AdminModel::G()->selectInput($input);
		

        [$items,$total] = AdminModel::G()->doSelect($where, $field, $order);
		
        if ('select' ===  ($input['format']??null)) {
            return $this->formatSelect($items,$format,$limit);
        }
		
		// 后处理
		$roles_map = AdminRoleModel::G()->getAdminRoles(array_column($items, 'id'));
        foreach ($items as $index => $item) {
            $admin_id = $item['id'];
            $items[$index]['roles'] = isset($roles_map[$admin_id]) ? implode(',', $roles_map[$admin_id]) : '';
            $items[$index]['show_toolbar'] = $admin_id != $login_admin_id;
        }
		return [$items,$total];
	}
	public function addAdmin($op_id, $input)
	{
		$role_ids=$input['roles'];
		// 这里要区分操作的 admin_id和得到的 admin_id;
		$role_ids = $role_ids ? explode(',', $role_ids) : [];
		static::ThrowOn(!$role_ids,'至少选择一个角色组',1);
		
		// 这里两个
		$flag = CommonService::G()->noRole($op_id,$role_ids, false);
		static::ThrowOn($flag,'角色超出权限范围',1);
		
		if (false &&!Auth::isSupperAdmin() && $this->dataLimit) {
            if (!empty($data[$this->dataLimitField])) {
                $admin_id = $data[$this->dataLimitField];
                if (!in_array($admin_id, Auth::getScopeAdminIds(true))) {
                    throw new BusinessException('无数据权限');
                }
            }
        }
		$data = AdminModel::G()->inputFilter($input);
		$admin_id = AdminModel::G()->addAdmin($data);
		AdminRoleModel::G()->renew($admin_id,$role_ids);
		
		return $admin_id;
	}
	public function updateAdmin($op_id,$input)
	{
	// 这里要区分操作的 admin_id和得到的 admin_id;
		$admin_id = $input['id'];
		$role_ids = $input['roles'];
		$data = AdminModel::G()->inputFilter($input);
		
		static::ThrowOn(!$admin_id,'缺少参数',1);
		
		// 不能禁用自己
		if (isset($data['status']) && $data['status'] == 1 && $admin_id == $op_id) {
			static::ThrowOn(true,'不能禁用自己',1);
		}

		// 需要更新角色
		if ($role_ids !== null) {
			static::ThrowOn(!$role_ids,'至少选择一个角色组',1);
			
			$role_ids = explode(',', $role_ids);
			$exist_role_ids = AdminRoleModel::G()->rolesByAdmin($admin_id);
			
			$operator = AdminModel::G()->getAdminById($op_id);
			
			$scope_role_ids = AdminRoleModel::G()->rolesByAdmin($op_id);
			
			$is_supper_admin = $this->isSupperAdmin($op_id);
			if (!$is_supper_admin && !array_intersect($exist_role_ids, $scope_role_ids)) {
				static::ThrowOn(true,'无权限更改该记录',1);
			}
			if (!$is_supper_admin && array_diff($role_ids, $scope_role_ids)) {
				static::ThrowOn(true,'角色超出权限范围',1);
			}

			AdminRoleModel::G()->updateAdminRole($admin_id, $exist_role_ids, $role_ids);
		}
		AdminModel::G()->updateAdmin($admin_id, $data);
		return;
	}
	public function deleteAdmin($op_id, $ids)
	{
        if (!$ids) {
            return true;
        }
        $ids = (array)$ids;
		static::ThrowOn(in_array($op_id, $ids),'不能删除自己',1);
		
        if (false && !Auth::isSupperAdmin() && array_diff($ids, Auth::getScopeAdminIds())) {
			static::ThrowOn(true,'无数据权限',1);
        }
		
		AdminModel::G()->deleteByIds($ids);
        AdminRoleModel::G()->deleteByAdminIds($ids);
		
        return true;
	}
}