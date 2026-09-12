<?php declare(strict_types=1);
/**
 * DuckPhp Admin System - Permission Service
 * 权限业务服务（业务逻辑层）
 *
 * 与 MenuConfigService 的分工：
 * - PermissionService：权限校验（checkUserUrl）、权限 id 查询
 * - MenuConfigService：菜单结构的扫描、导入、构建
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
     * 用户是否拥有指定 url 的权限(超管全放行)
     * url 与库中存储一致:无域名的完整 path(含挂载前缀,如 /admin/Role/index)
     */
    public function checkUserUrl(int $userId, string $url): bool
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

    /**
     * 获取用户可见菜单树（精简结构，无 type=2）
     * 委托给 MenuConfigService::buildUserMenuTree()
     */
    public function getUserMenus(int $userId): array
    {
        return MenuConfigService::_()->buildUserMenuTree($userId);
    }
}
