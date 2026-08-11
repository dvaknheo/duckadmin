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
        $sql = "INSERT INTO admin_permissions (name, url, type, parent_id, weight, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $this->execute($sql, [
            $data['name'], $data['url'] ?? '', $data['type'] ?? 1,
            $data['parent_id'] ?? 0, $data['weight'] ?? 0,
            $data['created_at'], $data['updated_at']
        ]);
        return (int)$this->lastInsertId();
    }

    public function edit(int $id, array $data): bool
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $sql = "UPDATE admin_permissions SET name = ?, url = ?, type = ?, parent_id = ?, weight = ?, updated_at = ? WHERE id = ?";
        $this->execute($sql, [
            $data['name'], $data['url'] ?? '', $data['type'] ?? 1,
            $data['parent_id'] ?? 0, $data['weight'] ?? 0,
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
        $system_id = $this->create(['name' => '系统管理', 'url' => '', 'type' => 0, 'parent_id' => 0, 'weight' => 5]);
        // 菜单(按显示顺序 weight 递增)
        $user_id = $this->create(['name' => '用户管理', 'url' => 'user/index', 'type' => 1, 'parent_id' => $system_id, 'weight' => 10]);
        $role_id = $this->create(['name' => '角色管理', 'url' => 'role/index', 'type' => 1, 'parent_id' => $system_id, 'weight' => 20]);
        $perm_id = $this->create(['name' => '权限管理', 'url' => 'permission/index', 'type' => 1, 'parent_id' => $system_id, 'weight' => 30]);
        // 用户操作
        foreach (['create', 'edit', 'delete'] as $i => $action) {
            $this->create(['name' => '用户' . $action, 'url' => 'user/' . $action, 'type' => 2, 'parent_id' => $user_id, 'weight' => 10 + $i + 1]);
        }
        // 角色操作
        foreach (['create', 'edit', 'delete'] as $i => $action) {
            $this->create(['name' => '角色' . $action, 'url' => 'role/' . $action, 'type' => 2, 'parent_id' => $role_id, 'weight' => 20 + $i + 1]);
        }
        // 权限操作
        foreach (['create', 'edit', 'delete'] as $i => $action) {
            $this->create(['name' => '权限' . $action, 'url' => 'permission/' . $action, 'type' => 2, 'parent_id' => $perm_id, 'weight' => 30 + $i + 1]);
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
     * 获取用户可见菜单树(type 0/1 按 parent_id 组树)
     * 超级管理员角色直接返回全部菜单,其余按角色规则过滤
     */
    public function getUserMenus(int $userId): array
    {
        if (RoleModel::_()->isSuperRole($userId)) {
            $sql = "SELECT id, name, url, type, parent_id, weight FROM admin_permissions
                    WHERE deleted_at IS NULL AND type IN (0,1)
                    ORDER BY weight ASC, id ASC";
            $rows = $this->fetchAll($sql);
        } else {
            $sql = "SELECT DISTINCT p.id, p.name, p.url, p.type, p.parent_id, p.weight
                    FROM admin_permissions p
                    INNER JOIN admin_role_permissions rp ON p.id = rp.permission_id
                    INNER JOIN admin_role_users ru ON rp.role_id = ru.role_id
                    WHERE ru.user_id = ? AND p.deleted_at IS NULL AND p.type IN (0,1)
                    ORDER BY p.weight ASC, p.id ASC";
            $rows = $this->fetchAll($sql, [$userId]);
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
}
