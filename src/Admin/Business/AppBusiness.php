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
use DuckPhp\Core\App;
use DuckPhp\GlobalAdmin\GlobalAdmin;

class AppBusiness extends Base
{
    public function canAccess($admin_id, $class = null, $method = null, ?string $url = null): bool
    {
        if (empty($url)) {
            return true;
        }
        return $this->checkUserUrl((int)$admin_id, $url);
    }

    /**
     * 用户是否拥有指定 url 的权限(超管全放行)
     * url 与库中存储一致:无域名的完整 path(含挂载前缀,如 /admin/Role/index)
     */
    protected function checkUserUrl(int $userId, string $url): bool
    {
        if (RoleUserModel::_()->isSuperRole($userId)) {
            return true;
        }
        $path = (string)(parse_url($url, PHP_URL_PATH) ?: $url);
        $path = '/' . ltrim($path, '/');
        if ($path === '/' || $path === '/index' || preg_match('#(^|/)Home/index$#', $path)) {
            return true; // 首页/仪表盘放行
        }
        if (preg_match('#(^|/)Role/permissions$#', $path)) {
            return true; // 分配权限页:访问由 RoleController 内部按职位管理范围控制
        }
        $count = PermissionModel::_()->countUserUrlPermissions($userId, $path);
        return $count > 0;
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

        // 从 config/scanned_menu.php 导入菜单（为空则扫描路由），导入数据库
        $this->installMenus();

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
    public function installMenus(): array
    {
        $prefix = App::_()->options['controller_url_prefix'];

        $filename = App::_()->getConfigFile(App::_()->options['admin_menu_config_file']?? 'AdminMenu.php');
        $menuTree = include $filename;        
        
        $menuTree = (new AdminTreeBuilder())->resolveUrls($menuTree, $prefix);
        return PermissionModel::_()->importMenu($menuTree);
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
        $admin_id = (int)$admin_id;
        if (RoleUserModel::_()->isSuperRole($admin_id)) {
            $rows = PermissionModel::_()->getAllMenuItems();
        } else {
            $rows = PermissionModel::_()->getMenuItemsByUser($admin_id);
        }

        // 组树
        $tree = PermissionModel::_()->buildMenuTree($rows);

        // 精简：去掉 type 字段和空 children
        return (new AdminTreeBuilder())->simplifyTree($tree);
    }
}