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
        if (empty($input['key'])) {
            return ['success' => false, 'message' => '权限标识不能为空'];
        }
        PermissionModel::_()->create($input);
        return ['success' => true, 'message' => '创建成功'];
    }
    
    public function update(int $id, array $input): array
    {
        if (empty($input['name'])) {
            return ['success' => false, 'message' => '权限名称不能为空'];
        }
        if (empty($input['key'])) {
            return ['success' => false, 'message' => '权限标识不能为空'];
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
}
