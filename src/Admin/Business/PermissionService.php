<?php declare(strict_types=1);
/**
 * DuckPhp Admin System - Permission Service
 * 权限业务服务（业务逻辑层）
 */
namespace DuckAdmin\Admin\Business;

use DuckAdmin\Admin\Model\PermissionModel;
use DuckAdmin\Admin\Model\RoleUserModel;

class PermissionService extends Base
{
    /**
     * 用户已拥有的权限 id 集合(超管=全部权限)
     */
    public function getUserPermissionIds(int $userId): array
    {
        if (RoleUserModel::_()->isSuperRole($userId)) {
            return PermissionModel::_()->getAllIds();
        } else {
            return PermissionModel::_()->getUserPermissionIdsByRoles($userId);
        }
    }

    /**
     * 用户是否拥有指定 url 的权限(去掉 query 精确匹配;首页/空 url 放行;超管全放行)
     */
    public function checkUserUrl(int $userId, string $url): bool
    {
        if (RoleUserModel::_()->isSuperRole($userId)) {
            return true;
        }
        $path = (string)(parse_url($url, PHP_URL_PATH) ?: $url);
        $path = ltrim($path, '/');
        // 去掉 admin 挂载前缀
        if (strpos($path, 'admin/') === 0) {
            $path = substr($path, 6);
        }
        if ($path === '' || $path === 'index' || $path === 'Home/index') {
            return true; // 首页/仪表盘放行
        }
        if ($path === 'Role/permissions') {
            return true; // 分配权限页:访问由 RoleController 内部按职位管理范围控制
        }
        $count = PermissionModel::_()->countUserUrlPermissions($userId, $path);
        return $count > 0;
    }

    /**
     * 获取用户可见菜单树(type 0/1 按 parent_id 组树)
     * 超级管理员职位直接返回全部菜单,其余按职位规则过滤
     */
    public function getUserMenus(int $userId): array
    {
        if (RoleUserModel::_()->isSuperRole($userId)) {
            $rows = PermissionModel::_()->getAllMenuItems();
        } else {
            $rows = PermissionModel::_()->getMenuItemsByUser($userId);
        }
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
        return $tree;
    }
}
