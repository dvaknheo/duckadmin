<?php declare(strict_types=1);
/**
 * DuckPhp Admin System - Auth Business
 * 无状态：仅做密码校验，不涉及 Session
 */
namespace DuckAdmin\Admin\Business;

use DuckAdmin\Admin\Model\AdminModel;
use DuckAdmin\Admin\Model\PermissionModel;
use DuckAdmin\Admin\Model\RoleModel;
use DuckAdmin\Admin\Model\RoleUserModel;
use DuckPhp\GlobalAdmin\GlobalAdmin;

class AppBusiness extends Base
{
    public function canAccess($admin_id, $class = null, $method = null, ?string $url = null): bool
    {
        if (empty($url)) {
            return true;
        }
        return PermissionService::_()->checkUserUrl((int)$admin_id, $url);
    }
    public function log($admin_id, string $string, ?string $type = null, array $ext = [])
    {
        return;
    }

    public function isSuper($admin_id): bool
    {
        return RoleUserModel::_()->isSuperRole((int)$admin_id);
    }

    public function checkInstall(array $post): array
    {
        $admin_name = (string)($post['admin_name'] ?? '');
        $password = (string)($post['admin_password'] ?? '');
        $password_confirm = (string)($post['admin_password_confirm'] ?? '');

        //TODO 这里应该用 Validator;
        // Helper::ThrowOn(!$user, '请填写管理员账号');
        // Helper::ThrowOn(!$user, '请填写管理员账号');
        // Helper::ThrowOn(!$user, '请填写管理员账号');
        // Helper::ThrowOn(!$user, '请填写管理员账号');

        if ($admin_name === '') {
            throw new \Exception('请填写管理员账号');
        }
        if ($password === '') {
            throw new \Exception('请填写管理员密码');
        }
        if (strlen($password) < 6) {
            throw new \Exception('管理员密码至少 6 位');
        }
        if ($password !== $password_confirm) {
            throw new \Exception('两次输入的密码不一致');
        }
        return [];
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

        // 默认角色
        $super_role_id = RoleModel::_()->seedDefaultRoles();

        // 插入默认权限种子(目录→菜单→操作 三级)
        $this->seedDefaultPermissions();

        // 创建管理员并关联超级管理员角色
        AdminModel::_()->create([
            'username' => $username,
            'password' => $password,
            'realname' => $realname,
            'email' => (string)($input['admin_email'] ?? ''),
            'status' => 1,
        ]);
        $admin_id = (int)AdminModel::_()->lastInsertId();
        RoleUserModel::_()->setUserRoles($admin_id, [$super_role_id]);

        // 超级管理员拥有全部权限
        PermissionModel::_()->grantAllPermissions($super_role_id);

        return true;
    }

    /**
     * 插入默认权限种子(目录→菜单→操作 三级)
     * type: 0=目录 1=菜单 2=操作
     */
    protected function seedDefaultPermissions(): void
    {
        // 目录
        $system_dir = PermissionModel::_()->create(['name' => '系统管理', 'url' => '', 'type' => 0, 'parent_id' => 0, 'weight' => 5]);

        // 人员管理
        $user_id = PermissionModel::_()->create(['name' => '人员管理', 'url' => 'Admin/index', 'type' => 1, 'parent_id' => $system_dir, 'weight' => 10]);
        PermissionModel::_()->create(['name' => '新增人员', 'url' => 'Admin/create', 'type' => 2, 'parent_id' => $user_id, 'weight' => 11]);
        PermissionModel::_()->create(['name' => '编辑人员', 'url' => 'Admin/edit', 'type' => 2, 'parent_id' => $user_id, 'weight' => 12]);
        PermissionModel::_()->create(['name' => '删除人员', 'url' => 'Admin/delete', 'type' => 2, 'parent_id' => $user_id, 'weight' => 13]);

        // 职位管理
        $role_id = PermissionModel::_()->create(['name' => '职位管理', 'url' => 'Role/index', 'type' => 1, 'parent_id' => $system_dir, 'weight' => 20]);
        PermissionModel::_()->create(['name' => '新增职位', 'url' => 'Role/create', 'type' => 2, 'parent_id' => $role_id, 'weight' => 21]);
        PermissionModel::_()->create(['name' => '编辑职位', 'url' => 'Role/edit', 'type' => 2, 'parent_id' => $role_id, 'weight' => 22]);
        PermissionModel::_()->create(['name' => '删除职位', 'url' => 'Role/delete', 'type' => 2, 'parent_id' => $role_id, 'weight' => 23]);

        // 权限分配（超管专属）
        PermissionModel::_()->create(['name' => '权限分配', 'url' => 'Permission/index', 'type' => 1, 'parent_id' => $role_id, 'weight' => 25]);

        // 菜单管理（超管专属）
        $menu_id = PermissionModel::_()->create(['name' => '菜单管理', 'url' => 'Menu/index', 'type' => 1, 'parent_id' => $system_dir, 'weight' => 30]);
        PermissionModel::_()->create(['name' => '一键扫描', 'url' => 'Menu/scan', 'type' => 2, 'parent_id' => $menu_id, 'weight' => 31]);
        PermissionModel::_()->create(['name' => '新增菜单', 'url' => 'Menu/create', 'type' => 2, 'parent_id' => $menu_id, 'weight' => 32]);
        PermissionModel::_()->create(['name' => '编辑菜单', 'url' => 'Menu/edit', 'type' => 2, 'parent_id' => $menu_id, 'weight' => 33]);
        PermissionModel::_()->create(['name' => '删除菜单', 'url' => 'Menu/delete', 'type' => 2, 'parent_id' => $menu_id, 'weight' => 34]);
    }
    public function login($post)
    {
        Helper::FireGlobalEvent(GlobalAdmin::EVENT_SERVICE_ADMIN_LOGINING, $post);
        $username = $post['username'];
        $password = $post['password'];
        Helper::ThrowOn((empty($username) || empty($password)), '请输入用户名和密码');
        $admin = AdminModel::_()->getByUsername($username);
        Helper::ThrowOn(!$admin, "用户名或密码错误1");
        Helper::ThrowOn($admin['status'] == 0, "该账号已被禁用");
        Helper::ThrowOn(!password_verify($password, $admin['password']), '用户名或密码错误2:');
        
        // 更新最后登录时间
        AdminModel::_()->updateLoginTime((int)$admin['id']);
        $ret = [
            'id' => (int)$admin['id'],
            'name' => $username,
            'realname' => $admin['realname'] ?? $username,
        ];
        

        Helper::FireGlobalEvent(GlobalAdmin::EVENT_SERVICE_ADMIN_LOGINED, $ret);
        return $ret;
        
    }
    public function logout($admin_id)
    {
        Helper::FireGlobalEvent(GlobalAdmin::EVENT_SERVICE_ADMIN_LOGOUTING, $admin_id);
        Helper::FireGlobalEvent(GlobalAdmin::EVENT_SERVICE_ADMIN_LOGOUTED, $admin_id);
        return;
    }
    public function loadMenus($admin_id)
    {
        return PermissionService::_()->getUserMenus($admin_id);
    }
}