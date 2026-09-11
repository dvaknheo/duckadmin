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

    /**
     * 一键扫描:RouteLister 扫描 admin 路由
     * - 控制器类 @menu_group → 目录(type0,url 空)
     * - 方法 @menu → 该目录下菜单(type1);方法 @action → 该目录下权限(type2);二者二选一
     * 已有 url 跳过(source=1 标记自动扫描)
     * @return array<string> 新增的 url 列表
     */
    public function scanRoutes(): array
    {
        $routes = \DuckPhp\Component\RouteLister::_()->listAll(true, true, true);
        $groups = [];
        foreach ($routes as $route) {
            $controller = (string)($route['controller'] ?? '');
            $method = (string)($route['method'] ?? '');
            $url = (string)($route['url'] ?? '');
            if ($controller === '' || $method === '' || $url === '') {
                continue;
            }
            $path = ltrim($url, '/');
            if (strpos($path, 'admin/') === 0) {
                $path = substr($path, 6);
            }
            if ($path === '') {
                continue;
            }
            $groups[$controller][$method] = $path;
        }

        $model = PermissionModel::_();
        $existing = [];
        foreach ($model->getAll() as $p) {
            $existing[$p['url']] = (int)$p['id'];
        }
        $added = [];
        $weight = 100;

        foreach ($groups as $controller => $methods) {
            $groupName = $this->getMenuGroupFromAnnotation($controller);
            if ($groupName === '') {
                continue; // 无 @menu_group 的控制器不扫描
            }
            // 目录(按 name 查,url 为空)
            $dirId = $model->getDirIdByName($groupName);
            if (!$dirId) {
                $dirId = $model->create([
                    'name' => $groupName,
                    'url' => '',
                    'type' => 0,
                    'parent_id' => 0,
                    'weight' => $weight,
                    'source' => 1,
                ]);
                $weight += 10;
            }
            // 方法:@menu 菜单 / @action 操作(二选一)
            foreach ($methods as $method => $path) {
                if (isset($existing[$path])) {
                    continue;
                }
                $menuName = $this->getMethodAnnotation($controller, $method, 'menu');
                if ($menuName !== '') {
                    $model->create([
                        'name' => $menuName,
                        'url' => $path,
                        'type' => 1,
                        'parent_id' => (int)$dirId,
                        'weight' => $weight,
                        'source' => 1,
                    ]);
                    $existing[$path] = true;
                    $added[] = $path;
                    $weight++;
                    continue;
                }
                $actionName = $this->getMethodAnnotation($controller, $method, 'action');
                if ($actionName !== '') {
                    $model->create([
                        'name' => $actionName,
                        'url' => $path,
                        'type' => 2,
                        'parent_id' => (int)$dirId,
                        'weight' => $weight,
                        'source' => 1,
                    ]);
                    $existing[$path] = true;
                    $added[] = $path;
                    $weight++;
                }
            }
        }
        return $added;
    }

    /**
     * 读取控制器类 @menu_group 注解作为目录名称
     */
    protected function getMenuGroupFromAnnotation(string $controller): string
    {
        if ($controller === '' || !class_exists($controller)) {
            return '';
        }
        try {
            $ref = new \ReflectionClass($controller);
            $doc = (string)$ref->getDocComment();
            if (preg_match('/@menu_group\s+([^*]+)/', $doc, $m)) {
                return trim($m[1]);
            }
        } catch (\Throwable $e) {
            // ignore
        }
        return '';
    }

    /**
     * 读取控制器方法注解(@menu 或 @action)
     */
    protected function getMethodAnnotation(string $controller, string $method, string $tag): string
    {
        if ($controller === '' || !class_exists($controller)) {
            return '';
        }
        try {
            $ref = new \ReflectionMethod($controller, $method);
            $doc = (string)$ref->getDocComment();
            if (preg_match('/@' . $tag . '\s+([^*]+)/', $doc, $m)) {
                return trim($m[1]);
            }
        } catch (\Throwable $e) {
            // ignore
        }
        return '';
    }
}
