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
        [$where, $format, $limit, $field, $order] = $this->selectInput($input, AdminModel::G()->table(), $dataLimit, $dataLimitField);
        [$items,$total] = AdminModel::G()->doSelect($where, $field, $order);
		
        if ('select' ===  ($input['format']??null)) {
            return $this->formatSelect($items,$format,$limit);
        }
		
		// 后处理
        $admin_ids = array_column($items, 'id');
		$roles_map = AdminRoleModel::G()->getAdminRoles($admin_ids);
		
        foreach ($items as $index => $item) {
            $admin_id = $item['id'];
            $items[$index]['roles'] = isset($roles_map[$admin_id]) ? implode(',', $roles_map[$admin_id]) : '';
            $items[$index]['show_toolbar'] = $admin_id != $login_admin_id;
        }
		return [$items,$total];
	}
	public function addAdmin($role_ids, $input)
	{
		// 这里要区分操作的 admin_id和得到的 admin_id;
		$role_ids = $role_ids ? explode(',', $role_ids) : [];
		static::ThrowOn(!$role_ids,'至少选择一个角色组',1);
		if (false && !Auth::isSupperAdmin() && array_diff($role_ids, Auth::getScopeRoleIds())) {
			static::ThrowOn(true,'角色超出权限范围',1);
		}
		
		if (!Auth::isSupperAdmin() && $this->dataLimit) {
            if (!empty($data[$this->dataLimitField])) {
                $admin_id = $data[$this->dataLimitField];
                if (!in_array($admin_id, Auth::getScopeAdminIds(true))) {
                    throw new BusinessException('无数据权限');
                }
            }
        }
		$data = AdminModel::G()->prepareInsert($input);
		$admin_id = AdminModel::G()->addAdmin($data);
		AdminRoleModel::G()->renew($admin_id,$role_ids);
		
		return $admin_id;
	}
	public function updateAdmin()
	{
	// 这里要区分操作的 admin_id和得到的 admin_id;
		$admin_id = $request->post('id');
		$role_ids = $request->post('roles');
		[$id, $data] = $this->updateInput($request);
		static::ThrowOn(!$admin_id,'缺少参数',1);
		
		// 不能禁用自己
		if (isset($data['status']) && $data['status'] == 1 && $id == admin_id()) {
			static::ThrowOn(true,'不能禁用自己',1);
		}

		// 需要更新角色
		if ($role_ids !== null) {
			static::ThrowOn(!$role_ids,'至少选择一个角色组',1);
			$role_ids = explode(',', $role_ids);

			$is_supper_admin = Auth::isSupperAdmin();
			$exist_role_ids = AdminRoleModel::G()->rolesByAdmin($admin_id);
			
			$scope_role_ids = Auth::getScopeRoleIds();
			if (!$is_supper_admin && !array_intersect($exist_role_ids, $scope_role_ids)) {
				static::ThrowOn(true,'无权限更改该记录',1);
			}
			if (!$is_supper_admin && array_diff($role_ids, $scope_role_ids)) {
				static::ThrowOn(true,'角色超出权限范围',1);
			}

			AdminRouteModel::G()->updateAdminRole($admin_id, $exist_role_ids, $role_ids);
		}

		$this->doUpdate($id, $data);
		return $this->json(0);
	}
	public function deleteAdmin()
	{
		$ids = $request->post($primary_key);
		$admin_id = admin_id();
		
        if (!$ids) {
            return true;
        }
        $ids = (array)$ids;
        if (in_array(, $ids)) {
			static::ThrowOn(true,'不能删除自己',1);
        }
        if (!Auth::isSupperAdmin() && array_diff($ids, Auth::getScopeAdminIds())) {
			static::ThrowOn(true,'无数据权限',1);
        }

		AdminModel::G()->deleteByIds($ids);
        AdminRoleModel::G()->deleteByAdminIds($ids);
		
        return true;
	}
}