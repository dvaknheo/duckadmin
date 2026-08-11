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
    public function checkAccess($admin_id, $class = null, $method = null, ?string $url = null): bool
    {
        if (empty($url)) {
            return true;
        }
        return PermissionModel::_()->checkUserUrl((int)$admin_id, $url);
    }
    public function log($admin_id, string $string, ?string $type = null, array $ext = [])
    {
        return;
    }

    public function isSuper($admin_id): bool
    {
        return RoleModel::_()->isSuperRole((int)$admin_id);
    }

    /**
     * 安装系统:插入默认角色/权限种子,创建管理员(表由安装器 doSchema 建)
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

        // 默认角色 + 权限种子
        $super_role_id = RoleModel::_()->seedDefaultRoles();
        PermissionModel::_()->seedDefaultPermissions();

        // 创建管理员并关联超级管理员角色
        AdminUserModel::_()->create([
            'username' => $username,
            'password' => $password,
            'realname' => $realname,
            'email' => (string)($input['admin_email'] ?? ''),
            'status' => 1,
        ]);
        $admin_id = (int)AdminUserModel::_()->lastInsertId();
        RoleModel::_()->setUserRoles($admin_id, [$super_role_id]);

        // 超级管理员拥有全部权限
        PermissionModel::_()->grantAllPermissions($super_role_id);

        return true;
    }
}