<?php declare(strict_types=1);
/**
 * DuckPhp Admin System - Admin Business
 * 人员管理业务
 */
namespace DuckAdmin\Admin\Business;

use DuckAdmin\Admin\Model\AdminUserModel;
use DuckAdmin\Admin\Model\RoleModel;

class AdminBusiness extends Base
{
    /**
     * 获取人员列表(仅自己及下属:职位在管理范围内)
     */
    public function getList(int $page, int $pageSize, string $search = ''): array
    {
        $admin_id = $this->getCurrentAdminId();
        $roleIds = RoleBusiness::_()->getManageableRoleIds($admin_id);
        return AdminUserModel::_()->getPageList($page, $pageSize, $search, $roleIds);
    }
    
    /**
     * 创建用户
     */
    public function create(array $input, array $roleIds = []): array
    {
        // 验证
        if (empty($input['username'])) {
            return ['success' => false, 'message' => '用户名不能为空'];
        }
        if (empty($input['password'])) {
            return ['success' => false, 'message' => '密码不能为空'];
        }
        if (strlen($input['password']) < 6) {
            return ['success' => false, 'message' => '密码长度至少6位'];
        }
        
        // 检查用户名唯一性
        $existing = AdminUserModel::_()->getByUsername($input['username']);
        if ($existing) {
            return ['success' => false, 'message' => '用户名已存在'];
        }
        
        // 职位范围校验:目标职位必须是当前管理员可管理的子职位
        $admin_id = $this->getCurrentAdminId();
        foreach ($roleIds as $rid) {
            if (!RoleBusiness::_()->canManageRole($admin_id, (int)$rid)) {
                return ['success' => false, 'message' => '目标职位不在你的管理范围内'];
            }
        }
        
        AdminUserModel::_()->create($input);
        $userId = AdminUserModel::_()->lastInsertId();
        
        // 分配角色
        if (!empty($roleIds)) {
            RoleModel::_()->setUserRoles((int)$userId, $roleIds);
        }
        
        return ['success' => true, 'message' => '创建成功'];
    }
    
    /**
     * 更新用户
     */
    public function update(int $id, array $input, array $roleIds = []): array
    {
        // 验证
        if (empty($input['username'])) {
            return ['success' => false, 'message' => '用户名不能为空'];
        }
        
        // 职位范围校验:目标职位必须是当前管理员可管理的子职位
        $admin_id = $this->getCurrentAdminId();
        foreach ($roleIds as $rid) {
            if (!RoleBusiness::_()->canManageRole($admin_id, (int)$rid)) {
                return ['success' => false, 'message' => '目标职位不在你的管理范围内'];
            }
        }
        
        AdminUserModel::_()->edit($id, $input);
        
        // 更新角色
        if (!empty($roleIds)) {
            RoleModel::_()->setUserRoles($id, $roleIds);
        }
        
        return ['success' => true, 'message' => '更新成功'];
    }
    
    /**
     * 删除用户
     */
    public function delete(int $id): bool
    {
        return AdminUserModel::_()->delete($id);
    }
    
    /**
     * 获取单个用户
     */
    public function getById(int $id): ?array
    {
        return AdminUserModel::_()->getById($id);
    }
}

