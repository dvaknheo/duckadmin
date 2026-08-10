<?php declare(strict_types=1);
/**
 * DuckPhp Admin System - Auth Business
 * 无状态：仅做密码校验，不涉及 Session
 */
namespace DuckAdmin\Admin\Business;

use DuckAdmin\Admin\Model\AdminUserModel;
use DuckAdmin\Admin\Model\PermissionModel;
use DuckAdmin\Admin\Model\RoleModel;

class AdminBusiness extends Base
{
    public function checkAccess($admin_id, string $class, string $method, ?string $url = null)
    {
        return;
    }
    public function log($admin_id, string $string, ?string $type = null, array $ext = [])
    {
        return;
    }

    public function isSuper($admin_id): bool
    {
        return true;
    }

    /**
     * 安装系统：创建超级管理员角色、基础权限种子、管理员账号
     * @param array<string, mixed> $input 含 admin_name / admin_password / admin_realname / admin_email
     */
    public function install(array $input): bool
    {
        $username = (string)($input['admin_name'] ?? '');
        $password = (string)($input['admin_password'] ?? '');
        $realname = (string)($input['admin_realname'] ?? '');
        if ($realname === '') {
            $realname = $username;
        }

        // 创建超级管理员角色
        $role_id = RoleModel::_()->create([
            'name' => '超级管理员',
            'description' => '系统内置超级管理员角色，拥有全部权限',
        ]);

        // 基础权限种子
        $parent_id = PermissionModel::_()->create([
            'name' => '系统管理',
            'key' => 'system',
            'description' => '系统管理',
            'parent_id' => 0,
            'sort_order' => 100,
        ]);
        foreach ([
            ['name' => '用户管理', 'key' => 'user', 'sort_order' => 10],
            ['name' => '角色管理', 'key' => 'role', 'sort_order' => 20],
            ['name' => '权限管理', 'key' => 'permission', 'sort_order' => 30],
        ] as $perm) {
            PermissionModel::_()->create([
                'name' => $perm['name'],
                'key' => $perm['key'],
                'description' => $perm['name'],
                'parent_id' => $parent_id,
                'sort_order' => $perm['sort_order'],
            ]);
        }

        // 创建管理员并关联超级管理员角色
        AdminUserModel::_()->create([
            'username' => $username,
            'password' => $password,
            'realname' => $realname,
            'email' => (string)($input['admin_email'] ?? ''),
            'status' => 1,
        ]);
        $admin_id = (int)AdminUserModel::_()->lastInsertId();
        RoleModel::_()->setUserRoles($admin_id, [$role_id]);

        return true;
    }
}