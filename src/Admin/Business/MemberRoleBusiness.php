<?php declare(strict_types=1);
/**
 * DuckPhp Admin System - Member Role Business
 * 下属角色管理业务（非超管用）
 */
namespace DuckAdmin\Admin\Business;

use DuckAdmin\Admin\Model\PermissionModel;
use DuckAdmin\Admin\Model\RoleModel;
use DuckAdmin\Admin\Model\RoleUserModel;

class MemberRoleBusiness extends Base
{
    public function getList(int $page, int $pageSize, string $search = ''): array
    {
        return RoleModel::_()->getPageList($page, $pageSize, $search);
    }
    
    public function create(int $adminId, string $name, string $description, int $pid = 0): array
    {
        if (empty($name)) {
            return ['success' => false, 'message' => '职位名称不能为空'];
        }
        if ($pid !== 0 && !$this->canManageRole($adminId, $pid)) {
            return ['success' => false, 'message' => '上级职位不在你的管理范围内'];
        }
        RoleModel::_()->create(['name' => $name, 'description' => $description, 'pid' => $pid]);
        return ['success' => true, 'message' => '创建成功'];
    }
    
    public function update(int $adminId, int $id, string $name, string $description, ?int $pid = null): array
    {
        if (empty($name)) {
            return ['success' => false, 'message' => '职位名称不能为空'];
        }
        if ($pid !== null && !$this->canManageRole($adminId, $pid)) {
            return ['success' => false, 'message' => '上级职位不在你的管理范围内'];
        }
        $data = ['name' => $name, 'description' => $description];
        if ($pid !== null) {
            $data['pid'] = $pid;
        }
        RoleModel::_()->edit($id, $data);
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
    
    public function getUserRoleIds(int $userId): array
    {
        return RoleUserModel::_()->getUserRoleIds($userId);
    }

    /**
     * 当前管理员可管理的职位 id(其职位的整棵子树;超管=全部职位)
     */
    public function getManageableRoleIds(int $adminId): array
    {
        if (AdminBusiness::_()->isSuper($adminId)) {
            return RoleModel::_()->getAllIds();
        }
        $ids = [];
        foreach (RoleUserModel::_()->getUserRoleIds($adminId) as $rid) {
            $ids = array_merge($ids, RoleModel::_()->getSubTreeIds((int)$rid));
        }
        return array_values(array_unique($ids));
    }

    /**
     * 目标职位是否在管理员可管理范围内
     */
    public function canManageRole(int $adminId, int $roleId): bool
    {
        return in_array($roleId, $this->getManageableRoleIds($adminId), true);
    }

    /**
     * 当前管理员可管理的职位列表(含 pid,超管=全部)
     */
    public function getAllManageable(int $adminId): array
    {
        $ids = $this->getManageableRoleIds($adminId);
        $all = RoleModel::_()->getAll();
        return array_values(array_filter($all, function ($r) use ($ids) {
            return in_array((int)$r['id'], $ids, true);
        }));
    }

    /**
     * 分配权限:目标职位须在管理范围内;只能分配目标职位上级职位拥有的权限(上级=根/超管 → 全部)
     */
    public function setPermissions(int $adminId, int $roleId, array $permissionIds): array
    {
        if (!$this->canManageRole($adminId, $roleId)) {
            return ['success' => false, 'message' => '目标职位不在你的管理范围内'];
        }
        $role = RoleModel::_()->getById($roleId);
        if (!$role) {
            return ['success' => false, 'message' => '职位不存在'];
        }
        $parent_id = (int)($role['pid'] ?? 0);
        if ($parent_id === 0) {
            return ['success' => false, 'message' => '根职位(超级管理员)权限不可修改'];
        }
        $parent = RoleModel::_()->getById($parent_id);
        if (!$parent || (int)($parent['is_super'] ?? 0) === 1) {
            // 上级是根/超管:允许全部权限
            $allowed = array_column(PermissionModel::_()->getAll(), 'id');
        } else {
            $allowed = PermissionModel::_()->getRolePermissionIds($parent_id);
        }
        foreach ($permissionIds as $pid) {
            if (!in_array((int)$pid, $allowed, true)) {
                return ['success' => false, 'message' => '只能分配上级职位拥有的权限'];
            }
        }
        PermissionModel::_()->setRolePermissions($roleId, $permissionIds);
        return ['success' => true, 'message' => '分配成功'];
    }
}
