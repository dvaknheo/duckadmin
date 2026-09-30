<?php declare(strict_types=1);

namespace DuckAdmin\Admin_LaravelModel\Overrided\Model\Adapter;

use DuckAdmin\Admin\Model\RoleUserModel as BaseRoleUserModel;
use DuckAdmin\Admin_LaravelModel\Overrided\Model\Eloquent\RoleUser;

class RoleUserModelAdapter extends BaseRoleUserModel
{
    public function getUserRoleId(int $userId): ?int
    {
        $roleId = RoleUser::where('user_id', $userId)->value('role_id');
        return $roleId ? (int)$roleId : null;
    }
    
    public function setUserRole(int $userId, ?int $roleId): void
    {
        RoleUser::where('user_id', $userId)->delete();
        
        if ($roleId !== null) {
            RoleUser::create([
                'user_id' => $userId,
                'role_id' => $roleId,
            ]);
        }
    }
}
