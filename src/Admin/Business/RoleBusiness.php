<?php declare(strict_types=1);
/**
 * DuckPhp Admin System - Role Business
 * 职位管理业务（超管用）
 */
namespace DuckAdmin\Admin\Business;

use DuckAdmin\Admin\Model\PermissionModel;
use DuckAdmin\Admin\Model\RoleModel;
use DuckAdmin\Admin\Model\RolePermissionModel;

class RoleBusiness extends Base
{
    public function getList(int $page, int $pageSize, string $search = ''): array
    {
        return RoleModel::_()->getPageList($page, $pageSize, $search);
    }

    public function create(string $name, string $description, int $pid = 0): int
    {
        Helper::ThrowOn(empty($name), '职位名称不能为空');
        RoleModel::_()->create(['name' => $name, 'description' => $description, 'pid' => $pid]);
        return (int)RoleModel::_()->lastInsertId();
    }

    public function update(int $id, string $name, string $description, int $pid): bool
    {
        Helper::ThrowOn(empty($name), '职位名称不能为空');
        return RoleModel::_()->edit($id, ['name' => $name, 'description' => $description, 'pid' => $pid]);
    }

    public function delete(int $id): bool
    {
        return RoleModel::_()->delete($id);
    }

    public function getRolePermissions(int $roleId): array
    {
        return RolePermissionModel::_()->getRolePermissionIds($roleId);
    }

    public function getAll(): array
    {
        return RoleModel::_()->getAll();
    }

    /**
     * 获取职位选项列表（带层级前缀，用于下拉框）
     */
    public function getRoleOptions(): array
    {
        $all = RoleModel::_()->getAll();
        $tree = $this->buildTree($all);
        $options = [];
        $this->flattenRoleOptions($tree, $options, 0);
        return $options;
    }

    /**
     * 扁平化职位树为选项列表（带缩进前缀）
     */
    protected function flattenRoleOptions(array $nodes, array &$options, int $level): void
    {
        foreach ($nodes as $node) {
            $indent = $level > 0 ? str_repeat("\u00a0\u00a0\u00a0", $level - 1) . ($level > 1 ? "\u00a0 " : '') : '';
            $prefix = $level > 0 ? ($level > 1 ? '├─ ' : '├─ ') : '';
            $options[] = [
                'id' => $node['id'],
                'name' => $indent . ($level > 0 ? $prefix : '') . $node['name'],
                'depth' => $level,
            ];
            if (!empty($node['children'])) {
                $this->flattenRoleOptions($node['children'], $options, $level + 1);
            }
        }
    }

    /**
     * 获取职位树形结构
     */
    public function getTree(string $search = ''): array
    {
        $all = RoleModel::_()->getAll();

        // 过滤搜索
        if ($search !== '') {
            $all = array_values(array_filter($all, function ($row) use ($search) {
                return stripos($row['name'], $search) !== false;
            }));
        }

        return $this->buildTree($all);
    }

    /**
     * 构建树形结构
     */
    protected function buildTree(array $items): array
    {
        $map = [];
        $tree = [];
        foreach ($items as $item) {
            $item['children'] = [];
            $map[$item['id']] = $item;
        }
        foreach ($map as $id => &$node) {
            $pid = (int)($node['pid'] ?? 0);
            if ($pid && isset($map[$pid])) {
                $map[$pid]['children'][] = &$node;
            } else {
                $tree[] = &$node;
            }
        }
        unset($node);
        return $tree;
    }

    public function getById(int $id): ?array
    {
        return RoleModel::_()->getById($id);
    }

    /**
     * 分配权限
     */
    public function setPermissions(int $roleId, array $permissionIds): void
    {
        $role = RoleModel::_()->getById($roleId);
        Helper::ThrowOn(!$role, '职位不存在');
        Helper::ThrowOn((int)($role['is_super'] ?? 0) === 1, '超级管理员职位权限不可修改');
        RolePermissionModel::_()->setRolePermissions($roleId, $permissionIds);
    }
}
