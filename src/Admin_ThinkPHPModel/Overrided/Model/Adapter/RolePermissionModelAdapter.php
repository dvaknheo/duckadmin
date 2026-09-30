<?php declare(strict_types=1);

namespace DuckAdmin\Admin_ThinkPHPModel\Overrided\Model\Adapter;

use DuckAdmin\Admin\Model\RolePermissionModel as BaseRolePermissionModel;
use DuckAdmin\Admin_ThinkPHPModel\Overrided\Model\RolePermission as ThinkRolePermission;

class RolePermissionModelAdapter extends BaseRolePermissionModel
{
    public function getRolePermissionIds(int $roleId): array
    {
        return ThinkRolePermission::where('role_id', $roleId)
            ->column('permission_id');
    }
    
    public function setRolePermissions(int $roleId, array $permissionIds): void
    {
        // 删除现有权限
        ThinkRolePermission::where('role_id', $roleId)->delete();
        
        // 批量插入新权限
        foreach ($permissionIds as $permId) {
            ThinkRolePermission::create([
                'role_id' => $roleId,
                'permission_id' => (int)$permId,
            ]);
        }
    }
    
    public function hasPermission(int $roleId, int $permissionId): bool
    {
        return (bool)ThinkRolePermission::where('role_id', $roleId)
            ->where('permission_id', $permissionId)
            ->find();
    }
}
