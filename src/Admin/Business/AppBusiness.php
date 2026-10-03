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
use DuckPhp\Core\SuperGlobal;
use DuckPhp\GlobalAdmin\GlobalAdmin;
class AppBusiness extends Base
{
    /**
     * 是否有权访问指定 url(超管全放行)
     *
     * 注意:参数顺序必须与 DuckPhp\GlobalAdmin\AdminServiceInterface::canAccess 一致 —— $url 在第二位。
     * 原来是 ($admin_id, $class, $method, $url),与 GlobalAdmin 的调用顺序相反,
     * 导致 checkUserUrl 收到的是方法名(如 'index')而不是路径,非超管永远匹配不到权限。
     *
     * @param int|string $admin_id
     */
    public function canAccess($admin_id, ?string $url = null, ?string $class = null, ?string $method = null): bool
    {
        $path = $this->resolvePermissionUrl($url);
        if ($path === '') {
            return true;
        }
        return $this->checkUserUrl((int)$admin_id, $path);
    }

    /**
     * 归一化权限匹配用的 url
     *
     * 优先用路由给的 path_info(含挂载前缀,如 /admin/Role/index);
     * 它不以 / 开头时视为不可靠,退回真实请求路径。
     */
    protected function resolvePermissionUrl(?string $url): string
    {
        $path = trim((string)$url);
        if ($path !== '' && strpos($path, '/') === 0) {
            return $path;
        }
        $uri = (string)(SuperGlobal::_()->_SERVER('REQUEST_URI', '') ?? '');
        $uri = (string)(parse_url($uri, PHP_URL_PATH) ?: '');
        if ($uri !== '') {
            return $uri;
        }
        return $path === '' ? '' : '/' . ltrim($path, '/');
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
        $roleId = RoleUserModel::_()->getUserRoleId($userId);
        if ($roleId === null) {
            return false;
        }
        $path = (string)(parse_url($url, PHP_URL_PATH) ?: $url);
        // 按URL找权限:走模型层公开方法,勿在 Business 里调用受保护的 fetch()
        $perm = PermissionModel::_()->getByUrl($path);
        if (!$perm) {
            return false;
        }
        //TODO 带 ＃的特殊权限
        return RolePermissionModel::_()->hasPermission($roleId, (int)$perm['id']);
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
        // 注意:必须用 create() 的返回值取新 id。
        // Eloquent / ThinkORM 适配器走各自的连接,DbManager 的 lastInsertId() 拿不到它们插入的 id
        // (原来这里取到 0,导致超管角色被绑到不存在的 user_id=0)
        $admin_id = (int)AdminModel::_()->create([
            'username' => $username,
            'password' => $password,
            'realname' => $realname,
            'email' => (string)($input['admin_email'] ?? ''),
            'status' => 1,
        ]);

        RoleUserModel::_()->setUserRole($admin_id, $super_role_id);

        Helper::FireGlobalEvent('installed', __CLASS__, $input);
        return true;
    }

    /**
     * 参数与 DuckPhp\GlobalAdmin\AdminLoginServiceInterface::login 对齐
     * @param array<string, mixed> $post
     */
    public function login(array $post)
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
    /**
     * 参数名与 DuckPhp\GlobalAdmin\AdminLoginServiceInterface::logout($id) 对齐
     * @param int|string $id
     */
    public function logout($id)
    {
        Helper::FireGlobalEvent(GlobalAdmin::EVENT_SERVICE_ADMIN_LOGOUTING, $id);
        Helper::FireGlobalEvent(GlobalAdmin::EVENT_SERVICE_ADMIN_LOGOUTED, $id);
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
}