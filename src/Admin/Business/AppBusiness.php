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
use DuckPhp\Component\Validator;
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
        //TODO 带 ＃的特殊权限
        $count = PermissionModel::_()->countUserUrlPermissions($userId, $path);
        return $count > 0;
    }
    public function log($admin_id, string $string, ?string $type = null, array $ext = [])
    {
        // 日志系统待完成
        return;
    }

    public function isSuper($admin_id): bool
    {
        return RoleUserModel::_()->isSuperRole((int)$admin_id);
    }

    public function checkInstall(array $post): array
    {
        Validator::_()->init([
            'admin_name' => 'required',
            'admin_password' => 'required|minLen:6',
            'admin_password_confirm' => 'required',
        ])->setMessage([
            'admin_name.required' => '请填写管理员账号',
            'admin_password.required' => '请填写管理员密码',
            'admin_password.minLen' => '管理员密码至少 6 位',
            'admin_password_confirm.required' => '请填写确认密码',
        ])->check($post);

        Helper::ThrowOn($post['admin_password'] !== $post['admin_password_confirm'], '两次输入的密码不一致');

        return [];
    }
    /**
     * 安装系统:插入默认角色/权限种子,创建管理员(表由安装器 doSchema 建)
     * @param array<string, mixed> $input 含 admin_name / admin_password / admin_realname / admin_email
     */
    public function install(array $input): bool
    {
        Helper::FireGlobalEvent('installing', __CLASS__, $input);

        $username = (string)($input['admin_name'] ?? '');
        $password = (string)($input['admin_password'] ?? '');
        $realname = (string)($input['admin_realname'] ?? '');
        if ($realname === '') {
            $realname = $username;
        }

        $super_role_id = RoleModel::_()->seedDefaultRoles();

        //  导入数据库
        $menuTree = (new AdminTreeBuilder)->loadAllAdminPermissionMenu();
        PermissionModel::_()->importMenu($menuTree);

        // 创建管理员并关联超级管理员角色
        AdminModel::_()->create([
            'username' => $username,
            'password' => $password,
            'realname' => $realname,
            'email' => (string)($input['admin_email'] ?? ''),
            'status' => 1,
        ]);
        $admin_id = (int)AdminModel::_()->lastInsertId();

        RoleUserModel::_()->setUserRoles($admin_id, [$super_role_id]); // RoleUserModel 要取消

        Helper::FireGlobalEvent('installed', __CLASS__, $input);
        return true;
    }

    public function login($post)
    {
        Helper::FireGlobalEvent(GlobalAdmin::EVENT_SERVICE_ADMIN_LOGINING, $post);

        // TODO 这段验证改用 validator
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
    public function menu($admin_id)
    {
        $admin_id = (int)$admin_id;
        if (RoleUserModel::_()->isSuperRole($admin_id)) {
            $data = PermissionModel::_()->getAllMenuItems();
        } else {
            $data = PermissionModel::_()->getMenuItemsByUser($admin_id);
        }

        // 组树
        $tree = AdminTreeBuilder::_()->recordsetToTree($data, 'id', 'parent_id', 0);
        $tree =  AdminTreeBuilder::_()->permissionMenuTreeToSideMenuTree($tree);
        return $tree;
    }
}