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
        $roleId = RoleUserModel::_()->getUserRoleId($userId);
        if ($roleId !== null && RoleModel::_()->isSuper($roleId)) {
            return null; // 超管不限制
        }
        if ($roleId === null) {
            return []; // 无角色，看不到任何人
        }
        // 获取所有子孙组
        return RoleModel::_()->getSubTreeIds($roleId);
    }

    /**
     * 创建用户
     * @param array $input 用户数据
     * @param int|null $roleId 职位ID（1对1）
     */
    public function create(array $input, ?int $roleId = null): int
    {
        Helper::ThrowOn(empty($input['username']), '用户名不能为空');
        Helper::ThrowOn(empty($input['password']), '密码不能为空');
        Helper::ThrowOn(strlen($input['password']) < 6, '密码长度至少6位');

        $existing = AdminModel::_()->getByUsername($input['username']);
        Helper::ThrowOn($existing, '用户名已存在');

        AdminModel::_()->create($input);
        $adminId = (int)AdminModel::_()->lastInsertId();

        // 设置用户职位（1对1）
        if ($roleId !== null) {
            RoleUserModel::_()->setUserRole($adminId, $roleId);
        }

        return $adminId;
    }

    /**
     * 更新用户
     * @param int $id 用户ID
     * @param array $input 用户数据
     * @param int|null $roleId 职位ID（1对1，null表示不修改）
     */
    public function update(int $id, array $input, ?int $roleId = null): bool
    {
        Helper::ThrowOn(empty($input['username']), '用户名不能为空');

        AdminModel::_()->edit($id, $input);

        // 设置用户职位（1对1）
        if ($roleId !== null) {
            RoleUserModel::_()->setUserRole($id, $roleId);
        }

        return true;
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
