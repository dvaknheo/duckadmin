<?php declare(strict_types=1);
/**
 * DuckPhp Admin System - Role Business
 * 职位管理业务（超管用）
 */
namespace DuckAdmin\Admin\Business;

use DuckAdmin\Admin\Model\RoleModel;

class RoleBusiness extends Base
{
    public function getList(int $page, int $pageSize, string $search = ''): array
    {
        return RoleModel::_()->getPageList($page, $pageSize, $search);
    }

    public function create(string $name, string $description, int $pid = 0): array
    {
        if (empty($name)) {
            return ['success' => false, 'message' => '职位名称不能为空'];
        }
        RoleModel::_()->create(['name' => $name, 'description' => $description, 'pid' => $pid]);
        return ['success' => true, 'message' => '创建成功'];
    }

    public function update(int $id, string $name, string $description, int $pid): array
    {
        if (empty($name)) {
            return ['success' => false, 'message' => '职位名称不能为空'];
        }
        RoleModel::_()->edit($id, ['name' => $name, 'description' => $description, 'pid' => $pid]);
        return ['success' => true, 'message' => '更新成功'];
    }

    public function delete(int $id): bool
    {
        return RoleModel::_()->delete($id);
    }

    public function getRolePermissions(int $roleId): array
    {
        return PermissionModel::_()->getRolePermissionIds($roleId);
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
    public function setPermissions(int $roleId, array $permissionIds): array
    {
        $role = RoleModel::_()->getById($roleId);
        if (!$role) {
            return ['success' => false, 'message' => '职位不存在'];
        }
        if ((int)($role['is_super'] ?? 0) === 1) {
            return ['success' => false, 'message' => '超级管理员职位权限不可修改'];
        }
        PermissionModel::_()->setRolePermissions($roleId, $permissionIds);
        return ['success' => true, 'message' => '分配成功'];
    }
}
