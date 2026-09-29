<?php declare(strict_types=1);
/**
 * DuckPhp Admin System - Auth Business
 * 无状态：仅做密码校验，不涉及 Session
 */
namespace DuckAdmin\Admin\Business;

use DuckAdmin\Admin\Model\AdminModel;
use DuckAdmin\Admin\Model\PermissionModel;
use DuckAdmin\Admin\Model\RoleModel;
use DuckAdmin\Admin\Model\RolePermissionModel;
use DuckAdmin\Admin\Model\RoleUserModel;
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
        if ($this->isUserSuper($userId)) {
            return true;
        }
        $path = (string)(parse_url($url, PHP_URL_PATH) ?: $url);
        $roleId = RoleUserModel::_()->getUserRoleId($userId);
        // 按URL找权限
        $perm = PermissionModel::_()->fetch("SELECT id FROM admin_permissions WHERE url = ? AND deleted_at IS NULL", [$path]);
        if (!$perm) {
            return false;
        }
        //TODO 带 ＃的特殊权限
        return RolePermissionModel::_()->hasPermission($roleId, $perm['id']);
    }

    /**
     * 检查用户是否为超级角色
     */
    protected function isUserSuper(int $userId): bool
    {
        $roleId = RoleUserModel::_()->getUserRoleId($userId);
        return $roleId !== null && RoleModel::_()->isSuper($roleId);
    }

    public function log($admin_id, string $string, ?string $type = null, array $ext = [])
    {
        // 日志系统待完成
        return;
    }

    public function isSuper($admin_id): bool
    {
        return $this->isUserSuper((int)$admin_id);
    }

    public function checkInstall(array $post): array
    {
        Helper::Validator()->init([
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
        $menuTree = Helper::_()->permissionMenu()->loadAll();
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

        RoleUserModel::_()->setUserRole($admin_id, $super_role_id);

        Helper::FireGlobalEvent('installed', __CLASS__, $input);
        return true;
    }

    public function login($post)
    {
        //TODO 事件名称整理一下
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
    public function menu($admin_id)
    {
        $admin_id = (int)$admin_id;
        if ($this->isUserSuper($admin_id)) {
            $data = PermissionModel::_()->getAllMenuItems();
        } else {
            $roleId = RoleUserModel::_()->getUserRoleId($admin_id);
            $data = PermissionModel::_()->getMenuItemsByRole($roleId);
        }

        // 组树
        $tree = Helper::_()->permissionMenu()->recordsetToTree($data, 'id', 'parent_id', 0);
        $tree = Helper::_()->permissionMenu()->permissionMenuTreeToSideMenuTree($tree);
        return $tree;
    }
    public function updateMenuConfigJson()
    {
        //
    }
}