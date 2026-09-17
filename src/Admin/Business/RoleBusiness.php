<?php declare(strict_types=1);
/**
 * DuckPhp Admin System - Role Business
 * 职位管理业务（超管用）
 */
namespace DuckAdmin\Admin\Business;

use DuckAdmin\Admin\Model\PermissionModel;
use DuckAdmin\Admin\Model\RoleModel;
use DuckAdmin\Admin\Model\RolePermissionModel;

class RoleBusiness extends Base
{
    public function getList(int $page, int $pageSize, string $search = ''): array
    {
        return RoleModel::_()->getPageList($page, $pageSize, $search);
    }

    public function create(string $name, string $description, int $pid = 0): int
    {
        Helper::ThrowOn(empty($name), '职位名称不能为空');
        RoleModel::_()->create(['name' => $name, 'description' => $description, 'pid' => $pid]);
        return (int)RoleModel::_()->lastInsertId();
    }

    public function update(int $id, string $name, string $description, int $pid): bool
    {
        Helper::ThrowOn(empty($name), '职位名称不能为空');
        return RoleModel::_()->edit($id, ['name' => $name, 'description' => $description, 'pid' => $pid]);
    }

    public function delete(int $id): bool
    {
        return RoleModel::_()->delete($id);
    }

    public function getRolePermissions(int $roleId): array
    {
        return RolePermissionModel::_()->getRolePermissionIds($roleId);
    }

    public function getAll(): array
    {
        return RoleModel::_()->getAll();
    }

    public function getById(int $id): ?array
    {
        return RoleModel::_()->getById($id);
    }

    /**
     * 分配权限
     */
    public function setPermissions(int $roleId, array $permissionIds): void
    {
        $role = RoleModel::_()->getById($roleId);
        Helper::ThrowOn(!$role, '职位不存在');
        Helper::ThrowOn((int)($role['is_super'] ?? 0) === 1, '超级管理员职位权限不可修改');
        RolePermissionModel::_()->setRolePermissions($roleId, $permissionIds);
    }
}
