<?php declare(strict_types=1);
/**
 * DuckPhp Admin System - Permission Business
 */
namespace DuckAdmin\Admin\Business;

use DuckAdmin\Admin\Model\PermissionModel;

class PermissionBusiness extends Base
{
    public function getList(int $page, int $pageSize, string $search = ''): array
    {
        return PermissionModel::_()->getPageList($page, $pageSize, $search);
    }
    
    public function create(array $input): array
    {
        if (empty($input['name'])) {
            return ['success' => false, 'message' => '权限名称不能为空'];
        }
        PermissionModel::_()->create($input);
        return ['success' => true, 'message' => '创建成功'];
    }
    
    public function update(int $id, array $input): array
    {
        if (empty($input['name'])) {
            return ['success' => false, 'message' => '权限名称不能为空'];
        }
        PermissionModel::_()->edit($id, $input);
        return ['success' => true, 'message' => '更新成功'];
    }
    
    public function delete(int $id): bool
    {
        return PermissionModel::_()->delete($id);
    }
    
    public function getAll(): array
    {
        return PermissionModel::_()->getAll();
    }
    
    public function getById(int $id): ?array
    {
        return PermissionModel::_()->getById($id);
    }

    /**
     * 当前管理员可分配的权限 id(自己已拥有,超管=全部权限)
     */
    public function getAssignablePermissionIds(int $adminId): array
    {
        return PermissionService::_()->getUserPermissionIds($adminId);
    }

    /**
     * 一键扫描路由入库
     */
    public function scanRoutes(): array
    {
        return PermissionModel::_()->scanRoutes();
    }
}
