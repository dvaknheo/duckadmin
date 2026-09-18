<?php declare(strict_types=1);
/**
 * DuckPhp Admin System - Menu Business
 * 菜单管理业务（超管用）
 */
namespace DuckAdmin\Admin\Business;

use DuckAdmin\Admin\Model\PermissionModel;

class MenuBusiness extends Base
{
    public function create(array $input): int
    {
        Helper::ThrowOn(empty($input['name']), '权限名称不能为空');
        return PermissionModel::_()->create($input);
    }

    public function update(int $id, array $input): bool
    {
        Helper::ThrowOn(empty($input['name']), '权限名称不能为空');
        return PermissionModel::_()->edit($id, $input);
    }

    public function delete(int $id): bool
    {
        Helper::ThrowOn(PermissionModel::_()->hasChildren($id), '该菜单含有子节点，禁止删除');
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
        return AdminTreeBuilder::_()->loadAll(true);
    }
    public function scanRoutes2(): array
    {
        $menuTree = AdminTreeBuilder::_()->loadAll();
        $added = PermissionModel::_()->importMenu($menuTree);
        return [$menuTree, $added];
    }
    /**
     * 获取菜单树（用于后台管理展示）
     * @param string $show 'all' 全部(含操作) / 'menu' 仅目录和菜单(不看操作)
     */
    public function getTree(string $show = 'all'): array
    {
        $all = PermissionModel::_()->getAll();
        if ($show === 'menu') {
            $all = array_values(array_filter($all, function ($row) {
                return (int) $row['type'] > 1;
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
            $pid = (int) ($node['parent_id'] ?? 0);
            if ($pid && isset($map[$pid])) {
                $map[$pid]['children'][] = &$node;
            } else {
                $tree[] = &$node;
            }
        }
        unset($node);
        return $tree;
    }
}
