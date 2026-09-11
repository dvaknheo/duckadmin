<?php declare(strict_types=1);
/**
 * DuckPhp Admin System - Permission Model
 */
namespace DuckAdmin\Admin\Model;

class PermissionModel extends Base
{
    public function getAll(): array
    {
        $sql = "SELECT * FROM admin_permissions WHERE deleted_at IS NULL ORDER BY weight ASC, id ASC";
        return $this->fetchAll($sql);
    }

    public function getById(int $id): ?array
    {
        $sql = "SELECT * FROM admin_permissions WHERE id = ? AND deleted_at IS NULL";
        $ret = $this->fetch($sql, [$id]);
        return $ret === false ? null : $ret;
    }

    public function getPageList(int $page, int $pageSize, string $search = ''): array
    {
        $where = "deleted_at IS NULL";
        $params = [];
        if ($search !== '') {
            $where .= " AND (name LIKE ? OR url LIKE ?)";
            $like = '%' . $search . '%';
            $params = [$like, $like];
        }
        $totalSql = "SELECT COUNT(*) as total FROM admin_permissions WHERE {$where}";
        $total = $this->fetch($totalSql, $params)['total'] ?? 0;
        $offset = ($page - 1) * $pageSize;
        $listSql = "SELECT * FROM admin_permissions WHERE {$where} ORDER BY weight ASC, id ASC LIMIT ? OFFSET ?";
        $listParams = array_merge($params, [$pageSize, $offset]);
        $list = $this->fetchAll($listSql, $listParams);
        return ['total' => (int)$total, 'list' => $list];
    }

    public function create(array $data): int
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        $sql = "INSERT INTO admin_permissions (name, url, type, parent_id, weight, source, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $this->execute($sql, [
            $data['name'], $data['url'] ?? '', $data['type'] ?? 1,
            $data['parent_id'] ?? 0, $data['weight'] ?? 0,
            $data['source'] ?? 0,
            $data['created_at'], $data['updated_at']
        ]);
        return (int)$this->lastInsertId();
    }

    public function edit(int $id, array $data): bool
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $sql = "UPDATE admin_permissions SET name = ?, url = ?, type = ?, parent_id = ?, weight = ?, source = ?, updated_at = ? WHERE id = ?";
        $this->execute($sql, [
            $data['name'], $data['url'] ?? '', $data['type'] ?? 1,
            $data['parent_id'] ?? 0, $data['weight'] ?? 0,
            $data['source'] ?? 0,
            $data['updated_at'], $id
        ]);
        return true;
    }

    public function delete(int $id): bool
    {
        $now = date('Y-m-d H:i:s');
        $sql = "UPDATE admin_permissions SET deleted_at = ?, updated_at = ? WHERE id = ?";
        $this->execute($sql, [$now, $now, $id]);
        return true;
    }

    public function getRolePermissionIds(int $roleId): array
    {
        $sql = "SELECT permission_id FROM admin_role_permissions WHERE role_id = ?";
        $rows = $this->fetchAll($sql, [$roleId]);
        return array_column($rows, 'permission_id');
    }

    public function setRolePermissions(int $roleId, array $permissionIds): void
    {
        $sql = "DELETE FROM admin_role_permissions WHERE role_id = ?";
        $this->execute($sql, [$roleId]);
        foreach ($permissionIds as $permId) {
            $sql = "INSERT INTO admin_role_permissions (role_id, permission_id) VALUES (?, ?)";
            $this->execute($sql, [$roleId, (int)$permId]);
        }
    }

    /**
     * 插入默认权限种子(目录→菜单→操作 三级)
     * type: 0=目录 1=菜单 2=操作
     */
    public function seedDefaultPermissions(): void
    {
        // 目录
        $system_dir = $this->create(['name' => '系统管理', 'url' => '', 'type' => 0, 'parent_id' => 0, 'weight' => 5]);
        // 人员管理(原用户管理)
        $user_id = $this->create(['name' => '人员管理', 'url' => 'User/index', 'type' => 1, 'parent_id' => $system_dir, 'weight' => 10]);
        foreach (['create', 'edit', 'delete'] as $i => $action) {
            $this->create(['name' => '人员' . $action, 'url' => 'User/' . $action, 'type' => 2, 'parent_id' => $user_id, 'weight' => 10 + $i + 1]);
        }
        // 职位管理(原角色管理)
        $role_id = $this->create(['name' => '职位管理', 'url' => 'Role/index', 'type' => 1, 'parent_id' => $system_dir, 'weight' => 20]);
        foreach (['create', 'edit', 'delete'] as $i => $action) {
            $this->create(['name' => '职位' . $action, 'url' => 'Role/' . $action, 'type' => 2, 'parent_id' => $role_id, 'weight' => 20 + $i + 1]);
        }
        $this->create(['name' => '分配权限', 'url' => 'Role/permissions', 'type' => 1, 'parent_id' => $role_id, 'weight' => 25]);
        // 权限和菜单管理(SystemController,超管专属)
        $sys_id = $this->create(['name' => '权限和菜单管理', 'url' => 'System/index', 'type' => 1, 'parent_id' => $system_dir, 'weight' => 30]);
        foreach (['scan', 'create', 'edit', 'delete'] as $i => $action) {
            $this->create(['name' => $action === 'scan' ? '一键扫描' : '菜单' . $action, 'url' => 'System/' . $action, 'type' => 2, 'parent_id' => $sys_id, 'weight' => 30 + $i + 1]);
        }
    }

    public function grantAllPermissions(int $roleId): void
    {
        $sql = "SELECT id FROM admin_permissions WHERE deleted_at IS NULL";
        $rows = $this->fetchAll($sql);
        $ids = array_column($rows, 'id');
        $this->setRolePermissions($roleId, $ids);
    }

    /**
     * 获取所有权限ID（供Service层使用）
     */
    public function getAllIds(): array
    {
        $sql = "SELECT id FROM admin_permissions WHERE deleted_at IS NULL";
        $rows = $this->fetchAll($sql);
        return array_column($rows, 'id');
    }

    /**
     * 获取用户在角色中拥有的权限ID（供Service层使用）
     */
    public function getUserPermissionIdsByRoles(int $userId): array
    {
        $sql = "SELECT DISTINCT p.id FROM admin_permissions p
                INNER JOIN admin_role_permissions rp ON p.id = rp.permission_id
                INNER JOIN admin_role_users ru ON rp.role_id = ru.role_id
                WHERE ru.user_id = ? AND p.deleted_at IS NULL";
        $rows = $this->fetchAll($sql, [$userId]);
        return array_column($rows, 'id');
    }

    /**
     * 获取用户拥有的菜单项（type 0/1）（供Service层使用）
     */
    public function getMenuItemsByUser(int $userId): array
    {
        $sql = "SELECT DISTINCT p.id, p.name, p.url, p.type, p.parent_id, p.weight
                FROM admin_permissions p
                INNER JOIN admin_role_permissions rp ON p.id = rp.permission_id
                INNER JOIN admin_role_users ru ON rp.role_id = ru.role_id
                WHERE ru.user_id = ? AND p.deleted_at IS NULL AND p.type IN (0,1)
                ORDER BY p.weight ASC, p.id ASC";
        return $this->fetchAll($sql, [$userId]);
    }

    /**
     * 获取所有菜单项（type 0/1）（供Service层使用）
     */
    public function getAllMenuItems(): array
    {
        $sql = "SELECT id, name, url, type, parent_id, weight FROM admin_permissions
                WHERE deleted_at IS NULL AND type IN (0,1)
                ORDER BY weight ASC, id ASC";
        return $this->fetchAll($sql);
    }

    /**
     * 检查用户是否有指定URL的权限（供Service层使用）
     */
    public function countUserUrlPermissions(int $userId, string $path): int
    {
        $sql = "SELECT COUNT(*) FROM admin_permissions p
                INNER JOIN admin_role_permissions rp ON p.id = rp.permission_id
                INNER JOIN admin_role_users ru ON rp.role_id = ru.role_id
                WHERE ru.user_id = ? AND p.deleted_at IS NULL AND p.url = ?";
        return (int)$this->fetchColumn($sql, [$userId, $path]);
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

        $existing = [];
        foreach ($this->getAll() as $p) {
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
            $dirRow = $this->fetch(
                "SELECT id FROM admin_permissions WHERE type = 0 AND name = ? AND deleted_at IS NULL",
                [$groupName]
            );
            $dirId = $dirRow ? (int)$dirRow['id'] : 0;
            if (!$dirId) {
                $dirId = $this->create([
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
                    $this->create([
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
                    $this->create([
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

}
