<?php declare(strict_types=1);
/**
 * DuckPhp Admin System - Menu Business
 * 菜单管理业务（超管用）
 */
namespace DuckAdmin\Admin\Business;

use DuckAdmin\Admin\Model\PermissionModel;

class MenuBusiness extends Base
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
     * 一键扫描路由入库
     */
    public function scanRoutes(): array
    {
        return PermissionModel::_()->scanRoutes();
    }
}
