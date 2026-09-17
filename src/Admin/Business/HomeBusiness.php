<?php declare(strict_types=1);
/**
 * DuckPhp Admin System - Home Business
 */
namespace DuckAdmin\Admin\Business;

use DuckAdmin\Admin\Model\AdminModel;
use DuckAdmin\Admin\Model\PermissionModel;
use DuckAdmin\Admin\Model\RoleModel;
use DuckAdmin\Admin\Model\RoleUserModel;

class HomeBusiness extends Base
{
    /**
     * 获取用户信息
     */
    public function getUser(int $userId): ?array
    {
        return AdminModel::_()->getById($userId);
    }

    /**
     * 更新个人信息
     * @param int $userId
     * @param string $realname
     * @param string $email
     * @param string $password 密码，留空表示不修改
     */
    public function updateProfile(int $userId, string $realname, string $email, string $password = ''): void
    {
        Helper::ThrowOn($realname === '', '姓名不能为空');

        $updateData = [
            'realname' => $realname,
            'email' => $email,
        ];

        if ($password !== '') {
            Helper::ThrowOn(strlen($password) < 6, '新密码长度至少6位');
            $updateData['password'] = $password;
        }

        AdminModel::_()->edit($userId, $updateData);
    }

    /**
     * 获取用户权限树
     */
    public function getUserPermissionTree(int $userId): array
    {
        // 检查是否是超管角色
        $roleId = RoleUserModel::_()->getUserRoleId($userId);
        $isSuper = $roleId !== null && RoleModel::_()->isSuper($roleId);

        if ($isSuper) {
            // 超管：获取所有权限
            $allPermissions = PermissionModel::_()->getAll();
            $permissionIds = array_column($allPermissions, 'id');
        } else {
            // 非超管：获取用户角色分配的权限
            $permissionIds = PermissionModel::_()->getRolePermissionIds($roleId ?? 0);
        }

        // 获取权限树
        $tree = MenuBusiness::_()->getTree('all');
        return [
            'tree' => $tree,
            'permission_ids' => $permissionIds,
            'is_super' => $isSuper,
        ];
    }

    /**
     * 获取用户菜单树（仅目录和菜单）
     */
    public function getUserMenuTree(int $userId): array
    {
        $roleId = RoleUserModel::_()->getUserRoleId($userId);
        $isSuper = $roleId !== null && RoleModel::_()->isSuper($roleId);

        if ($isSuper) {
            $items = PermissionModel::_()->getAllMenuItems();
        } else {
            $items = PermissionModel::_()->getMenuItemsByRole($roleId);
        }

        return [
            'tree' => $this->buildMenuTree($items),
            'is_super' => $isSuper,
        ];
    }

    /**
     * 构建菜单树形结构
     */
    protected function buildMenuTree(array $items): array
    {
        $map = [];
        $tree = [];
        foreach ($items as $item) {
            $item['children'] = [];
            $map[$item['id']] = $item;
        }
        foreach ($map as $id => &$node) {
            $pid = (int)($node['parent_id'] ?? 0);
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
