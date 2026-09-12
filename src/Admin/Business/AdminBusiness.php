<?php declare(strict_types=1);
/**
 * DuckPhp Admin System - Admin Business
 * 人员管理业务（超管用）
 */
namespace DuckAdmin\Admin\Business;

use DuckAdmin\Admin\Model\AdminModel;
use DuckAdmin\Admin\Model\RoleModel;
use DuckAdmin\Admin\Model\RoleUserModel;

class AdminBusiness extends Base
{
    /**
     * 获取人员列表（只显示当前用户角色组及其子孙组的用户）
     */
    public function getList(int $currentUserId, int $page, int $pageSize, array $search = []): array
    {
        $roleIds = $this->getVisibleRoleIds($currentUserId);
        return AdminModel::_()->getPageList($page, $pageSize, $search, $roleIds);
    }

    /**
     * 获取当前用户可见的角色组 id 列表
     * 超级管理员返回 null（不限制），否则返回自己角色组及其子孙组
     * @return array|null
     */
    protected function getVisibleRoleIds(int $userId): ?array
    {
        if (RoleUserModel::_()->isSuperRole($userId)) {
            return null; // 超管不限制
        }
        $roleIds = RoleUserModel::_()->getUserRoleIds($userId);
        if (empty($roleIds)) {
            return []; // 无角色，看不到任何人
        }
        // 获取所有子孙组
        $allIds = [];
        foreach ($roleIds as $roleId) {
            $subIds = RoleModel::_()->getSubTreeIds((int)$roleId);
            $allIds = array_merge($allIds, $subIds);
        }
        return array_unique($allIds);
    }

    /**
     * 创建用户
     */
    public function create(array $input): array
    {
        if (empty($input['username'])) {
            return ['success' => false, 'message' => '用户名不能为空'];
        }
        if (empty($input['password'])) {
            return ['success' => false, 'message' => '密码不能为空'];
        }
        if (strlen($input['password']) < 6) {
            return ['success' => false, 'message' => '密码长度至少6位'];
        }

        $existing = AdminModel::_()->getByUsername($input['username']);
        if ($existing) {
            return ['success' => false, 'message' => '用户名已存在'];
        }

        AdminModel::_()->create($input);

        return ['success' => true, 'message' => '创建成功'];
    }

    /**
     * 更新用户
     */
    public function update(int $id, array $input): array
    {
        if (empty($input['username'])) {
            return ['success' => false, 'message' => '用户名不能为空'];
        }

        AdminModel::_()->edit($id, $input);

        return ['success' => true, 'message' => '更新成功'];
    }

    /**
     * 删除用户
     */
    public function delete(int $id): bool
    {
        return AdminModel::_()->delete($id);
    }

    /**
     * 获取单个用户
     */
    public function getById(int $id): ?array
    {
        return AdminModel::_()->getById($id);
    }
}
