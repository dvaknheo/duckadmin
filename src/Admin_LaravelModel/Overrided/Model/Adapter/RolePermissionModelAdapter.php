<?php declare(strict_types=1);

namespace DuckAdmin\Admin_LaravelModel\Overrided\Model\Adapter;

use DuckAdmin\Admin\Model\RolePermissionModel as BaseRolePermissionModel;
use DuckAdmin\Admin_LaravelModel\Overrided\Model\Eloquent\RolePermission;

class RolePermissionModelAdapter extends BaseRolePermissionModel
{
    public function getRolePermissionIds(int $roleId): array
    {
        return RolePermission::where('role_id', $roleId)
            ->pluck('permission_id')
            ->toArray();
    }
    
    public function setRolePermissions(int $roleId, array $permissionIds): void
    {
        RolePermission::where('role_id', $roleId)->delete();
        
        foreach ($permissionIds as $permId) {
            RolePermission::create([
                'role_id' => $roleId,
                'permission_id' => (int)$permId,
            ]);
        }
    }
    
    public function hasPermission(int $roleId, int $permissionId): bool
    {
        return RolePermission::where('role_id', $roleId)
            ->where('permission_id', $permissionId)
            ->exists();
    }
}
