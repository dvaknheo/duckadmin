<?php
namespace DuckAdmin\Business;

use DuckAdmin\Model\AdminModel;
use DuckAdmin\Model\RoleModel;
/**
 * 管理员业务
 */
class AdminBusiness extends BaseBusiness
{

	public function showAdmin($input)
	{
		return [100,[]];
        [$where, $format, $limit, $field, $order] = $this->selectInput($input);//-->一大堆参数要定
		
        [$items,$total] = AdminModel::G()->doSelect($where, $field, $order);
		
        if ($format === 'select') {
            return $this->formatSelect($items,$format,$limit);
        }
		
		// 后处理
        $admin_ids = array_column($items, 'id');
        $roles = AdminRole::whereIn('admin_id', $admin_ids)->get();
        $roles_map = [];
        foreach ($roles as $role) {
            $roles_map[$role['admin_id']][] = $role['role_id'];
        }
        $login_admin_id = admin_id();
		
        foreach ($items as $index => $item) {
            $admin_id = $item['id'];
            $items[$index]['roles'] = isset($roles_map[$admin_id]) ? implode(',', $roles_map[$admin_id]) : '';
            $items[$index]['show_toolbar'] = $admin_id != $login_admin_id;
        }
	}
	public function addAdmin($input)
	{
		$data = $this->insertInput($input);
		$admin_id = $this->doInsert($data);
		$role_ids = $request->post('roles');
		$role_ids = $role_ids ? explode(',', $role_ids) : [];
		
		static::ThrowOn(!$role_ids,'至少选择一个角色组',1);
		
		if (!Auth::isSupperAdmin() && array_diff($role_ids, Auth::getScopeRoleIds())) {
			static::ThrowOn(true,'角色超出权限范围',1);
		}
		AdminRole::where('admin_id', $admin_id)->delete();
		foreach ($role_ids as $id) {
			$admin_role = new AdminRole;
			$admin_role->admin_id = $admin_id;
			$admin_role->role_id = $id;
			$admin_role->save();
		}
		return $admin_id;
	}
	public function updateAdmin()
	{
		[$id, $data] = $this->updateInput($request);
		$admin_id = $request->post('id');
		static::ThrowOn(!$admin_id,'缺少参数',1);
		
		// 不能禁用自己
		if (isset($data['status']) && $data['status'] == 1 && $id == admin_id()) {
			static::ThrowOn(true,'不能禁用自己',1);
		}

		// 需要更新角色
		$role_ids = $request->post('roles');
		if ($role_ids !== null) {
			static::ThrowOn(!$role_ids,'至少选择一个角色组',1);
			$role_ids = explode(',', $role_ids);

			$is_supper_admin = Auth::isSupperAdmin();
			$exist_role_ids = AdminRole::where('admin_id', $admin_id)->pluck('role_id')->toArray();
			$scope_role_ids = Auth::getScopeRoleIds();
			if (!$is_supper_admin && !array_intersect($exist_role_ids, $scope_role_ids)) {
				static::ThrowOn(true,'无权限更改该记录',1);
			}
			if (!$is_supper_admin && array_diff($role_ids, $scope_role_ids)) {
				static::ThrowOn(true,'角色超出权限范围',1);
			}

			// 删除账户角色
			$delete_ids = array_diff($exist_role_ids, $role_ids);
			AdminRole::whereIn('role_id', $delete_ids)->where('admin_id', $admin_id)->delete();
			// 添加账户角色
			$add_ids = array_diff($role_ids, $exist_role_ids);
			foreach ($add_ids as $role_id) {
				$admin_role = new AdminRole;
				$admin_role->admin_id = $admin_id;
				$admin_role->role_id = $role_id;
				$admin_role->save();
			}
		}

		$this->doUpdate($id, $data);
		return $this->json(0);
	}
	public function deleteAdmin()
	{
		$primary_key = $this->model->getKeyName();
        $ids = $request->post($primary_key);
        if (!$ids) {
            return true;
        }
        $ids = (array)$ids;
        if (in_array(admin_id(), $ids)) {
			static::ThrowOn(true,'不能删除自己',1);
        }
        if (!Auth::isSupperAdmin() && array_diff($ids, Auth::getScopeAdminIds())) {
			static::ThrowOn(true,'无数据权限',1);
        }
        $this->model->whereIn($primary_key, $ids)->delete();
        AdminRole::whereIn('admin_id', $ids)->delete();
		
        return true;
	}
}