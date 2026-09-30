<?php declare(strict_types=1);

namespace DuckAdmin\Admin_ThinkPHPModel\Overrided\Model\Adapter;

use DuckAdmin\Admin\Model\RoleUserModel as BaseRoleUserModel;
use DuckAdmin\Admin_ThinkPHPModel\Overrided\Model\RoleUser as ThinkRoleUser;

class RoleUserModelAdapter extends BaseRoleUserModel
{
    public function getUserRoleId(int $userId): ?int
    {
        $roleId = ThinkRoleUser::where('user_id', $userId)->value('role_id');
        return $roleId ? (int)$roleId : null;
    }
    
    public function setUserRole(int $userId, ?int $roleId): void
    {
        // 删除现有关系
        ThinkRoleUser::where('user_id', $userId)->delete();
        
        // 创建新关系
        if ($roleId !== null) {
            ThinkRoleUser::create([
                'user_id' => $userId,
                'role_id' => $roleId,
            ]);
        }
    }
}
