<?php declare(strict_types=1);
/**
 * DuckPhp Admin System - Menu Config Service
 * 菜单配置服务：导入数据库，构建用户菜单树
 *
 * 与 PermissionService 的区别：
 * - PermissionService：权限校验（checkUserUrl）、权限 id 查询
 * - MenuConfigService：菜单结构的导入、构建
 */
namespace DuckAdmin\Admin\Business;

use DuckAdmin\Admin\Model\PermissionModel;
use DuckAdmin\Admin\Model\RoleUserModel;

class MenuConfigService extends Base
{
    // ================================================================
    //  导入：树形精简结构 → admin_permissions 表
    // ================================================================

    /**
     * 把树形精简结构导入到 admin_permissions 表（幂等，按 url 判重）
     * 传入的 $menuTree 应已补全绝对 url（通过 AdminTreeBuilder::resolveUrls）
     *
     * @param array $menuTree 树形精简菜单结构（url 已补全）
     * @return array<string> 新增的菜单/操作 url 列表（目录静默创建不计入）
     */
    public function importToDb(array $menuTree): array
    {
        return PermissionModel::_()->importMenu($menuTree);
    }

    // ================================================================
    //  构建：数据库 → 用户可见菜单树（精简结构，无 type=2）
    // ================================================================

    /**
     * 从 DB 构建用户可见菜单树（过滤 type=2 操作，精简结构）
     *
     * 返回结构：
     *   [
     *     'name' => '系统管理', 'icon' => null, 'url' => '',
     *     'children' => [
     *       ['name' => '人员管理', 'icon' => null, 'url' => '/admin/Admin/index', 'children' => []],
     *     ],
     *   ]
     *
     * @param int $userId 用户 id
     * @return array 用户可见菜单树
     */
    public function buildUserMenuTree(int $userId): array
    {
        if (RoleUserModel::_()->isSuperRole($userId)) {
            $rows = PermissionModel::_()->getAllMenuItems();
        } else {
            $rows = PermissionModel::_()->getMenuItemsByUser($userId);
        }

        // 组树
        $map = [];
        foreach ($rows as $row) {
            $row['children'] = [];
            $map[$row['id']] = $row;
        }
        $tree = [];
        foreach ($map as $id => &$node) {
            $pid = (int)$node['parent_id'];
            if ($pid && isset($map[$pid])) {
                $map[$pid]['children'][] = &$node;
            } else {
                $tree[] = &$node;
            }
        }
        unset($node);

        // 精简：去掉 type 字段和空 children，过滤 type=2（已在 SQL 中过滤）
        return $this->simplifyTree($tree);
    }

    // ================================================================
    //  内部辅助方法
    // ================================================================

    /**
     * 精简树：委托给 AdminTreeBuilder::simplifyTree()
     */
    protected function simplifyTree(array $nodes): array
    {
        return (new AdminTreeBuilder())->simplifyTree($nodes);
    }
}
