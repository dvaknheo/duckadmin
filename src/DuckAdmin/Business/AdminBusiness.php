<?php
namespace DuckAdmin\Business;

use DuckAdmin\Model\AdminModel;
use DuckAdmin\Model\RoleModel;
/**
 * 管理员业务
 */
class AdminBusiness extends BaseBusiness
{

    /**
     * 查询前置
     * @param Request $request
     * @return array
     * @throws BusinessException
     */
    protected function selectInput(Request $request): array
    {
        $field = $request->get('field');
        $order = $request->get('order', 'asc');
        $format = $request->get('format', 'normal');
        $limit = (int)$request->get('limit', $format === 'tree' ? 1000 : 10);
        $limit = $limit <= 0 ? 10 : $limit;
        $order = $order === 'asc' ? 'asc' : 'desc';
        $where = $request->get();
        $page = (int)$request->get('page');
        $page = $page > 0 ? $page : 1;
        $table = config('plugin.admin.database.connections.mysql.prefix') . $this->model->getTable();

        $allow_column = Util::db()->select("desc `$table`");
        if (!$allow_column) {
            throw new BusinessException('表不存在');
        }
        $allow_column = array_column($allow_column, 'Field', 'Field');
        if (!in_array($field, $allow_column)) {
            $field = null;
        }
        foreach ($where as $column => $value) {
            if ($value === '' || !isset($allow_column[$column]) ||
                (is_array($value) && (in_array($value[0], ['', 'undefined']) || in_array($value[1], ['', 'undefined'])))) {
                unset($where[$column]);
            }
        }
        // 按照数据限制字段返回数据
		if ($this->dataLimit === 'auth') {
            $primary_key = $this->model->getKeyName();
            if (!Auth::isSupperAdmin() && (!isset($where[$primary_key]) || $this->dataLimitField != $primary_key)) {
                $where[$this->dataLimitField] = ['in', Auth::getScopeAdminIds(true)];
            }
        }
        return [$where, $format, $limit, $field, $order, $page];
    }
	
	public function showAdmin()
	{
		return [100,[]];
        [$where, $format, $limit, $field, $order] = $this->selectInput($request);
        $query = $this->doSelect($where, $field, $order);
        if ($format === 'select') {
            return $this->formatSelect($query->get());
        }
        $paginator = $query->paginate($limit);
        $items = $paginator->items();
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
	public function addAdmin($data)
	{
		$data = $this->insertInput($request);
		$admin_id = $this->doInsert($data);
		$role_ids = $request->post('roles');
		$role_ids = $role_ids ? explode(',', $role_ids) : [];
		if (!$role_ids) {
			return $this->json(1, '至少选择一个角色组');
		}
		if (!Auth::isSupperAdmin() && array_diff($role_ids, Auth::getScopeRoleIds())) {
			return $this->json(1, '角色超出权限范围');
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
		if (!$admin_id) {
			return $this->json(1, '缺少参数');
		}

		// 不能禁用自己
		if (isset($data['status']) && $data['status'] == 1 && $id == admin_id()) {
			return $this->json(1, '不能禁用自己');
		}

		// 需要更新角色
		$role_ids = $request->post('roles');
		if ($role_ids !== null) {
			if (!$role_ids) {
				return $this->json(1, '至少选择一个角色组');
			}
			$role_ids = explode(',', $role_ids);

			$is_supper_admin = Auth::isSupperAdmin();
			$exist_role_ids = AdminRole::where('admin_id', $admin_id)->pluck('role_id')->toArray();
			$scope_role_ids = Auth::getScopeRoleIds();
			if (!$is_supper_admin && !array_intersect($exist_role_ids, $scope_role_ids)) {
				return $this->json(1, '无权限更改该记录');
			}
			if (!$is_supper_admin && array_diff($role_ids, $scope_role_ids)) {
				return $this->json(1, '角色超出权限范围');
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
            return $this->json(0);
        }
        $ids = (array)$ids;
        if (in_array(admin_id(), $ids)) {
            return $this->json(1, '不能删除自己');
        }
        if (!Auth::isSupperAdmin() && array_diff($ids, Auth::getScopeAdminIds())) {
            return $this->json(1, '无数据权限');
        }
        $this->model->whereIn($primary_key, $ids)->delete();
        AdminRole::whereIn('admin_id', $ids)->delete();
        return $this->json(0);
	}
}