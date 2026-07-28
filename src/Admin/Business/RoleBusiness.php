<?php declare(strict_types=1);
/**
 * DuckPhp Admin System - Role Business
 */
namespace DuckAdmin\Admin\Business;

use DuckAdmin\Admin\Model\PermissionModel;
use DuckAdmin\Admin\Model\RoleModel;

class RoleBusiness extends Base
{
    public function getList(int $page, int $pageSize, string $search = ''): array
    {
        return RoleModel::_()->getPageList($page, $pageSize, $search);
    }
    
    public function create(string $name, string $description): array
    {
        if (empty($name)) {
            return ['success' => false, 'message' => '角色名称不能为空'];
        }
        RoleModel::_()->create(['name' => $name, 'description' => $description]);
        return ['success' => true, 'message' => '创建成功'];
    }
    
    public function update(int $id, string $name, string $description): array
    {
        if (empty($name)) {
            return ['success' => false, 'message' => '角色名称不能为空'];
        }
        RoleModel::_()->edit($id, ['name' => $name, 'description' => $description]);
        return ['success' => true, 'message' => '更新成功'];
    }
    
    public function delete(int $id): bool
    {
        return RoleModel::_()->delete($id);
    }
    
    public function setPermissions(int $roleId, array $permissionIds): void
    {
        PermissionModel::_()->setRolePermissions($roleId, $permissionIds);
    }
    
    public function getRolePermissions(int $roleId): array
    {
        return PermissionModel::_()->getRolePermissionIds($roleId);
    }
}
