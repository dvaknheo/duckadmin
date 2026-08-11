<?php declare(strict_types=1);
/**
 * DuckPhp Admin System - Permission Model
 */
namespace DuckAdmin\Admin\Model;

class PermissionModel extends Base
{
    public function getAll(): array
    {
        $sql = "SELECT * FROM admin_permissions WHERE deleted_at IS NULL ORDER BY sort_order ASC, id ASC";
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
            $where .= " AND (name LIKE ? OR `key` LIKE ?)";
            $like = '%' . $search . '%';
            $params = [$like, $like];
        }
        $totalSql = "SELECT COUNT(*) as total FROM admin_permissions WHERE {$where}";
        $total = $this->fetch($totalSql, $params)['total'] ?? 0;
        $offset = ($page - 1) * $pageSize;
        $listSql = "SELECT * FROM admin_permissions WHERE {$where} ORDER BY sort_order ASC, id ASC LIMIT ? OFFSET ?";
        $listParams = array_merge($params, [$pageSize, $offset]);
        $list = $this->fetchAll($listSql, $listParams);
        return ['total' => (int)$total, 'list' => $list];
    }

    public function create(array $data): int
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        $sql = "INSERT INTO admin_permissions (name, `key`, description, parent_id, sort_order, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $this->execute($sql, [
            $data['name'], $data['key'], $data['description'] ?? '',
            $data['parent_id'] ?? 0, $data['sort_order'] ?? 0,
            $data['created_at'], $data['updated_at']
        ]);
        return (int)$this->lastInsertId();
    }

    public function edit(int $id, array $data): bool
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $sql = "UPDATE admin_permissions SET name = ?, `key` = ?, description = ?, parent_id = ?, sort_order = ?, updated_at = ? WHERE id = ?";
        $this->execute($sql, [
            $data['name'], $data['key'], $data['description'] ?? '',
            $data['parent_id'] ?? 0, $data['sort_order'] ?? 0,
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

    public function getUserPermissionKeys(int $userId): array
    {
        $sql = "SELECT DISTINCT p.`key` FROM admin_permissions p
                INNER JOIN admin_role_permissions rp ON p.id = rp.permission_id
                INNER JOIN admin_role_users ru ON rp.role_id = ru.role_id
                WHERE ru.user_id = ? AND p.deleted_at IS NULL";
        $rows = $this->fetchAll($sql, [$userId]);
        return array_column($rows, 'key');
    }

    /**
     * 按 key 查权限 id(未找到返回 0)
     */
    protected function getIdByKey(string $key): int
    {
        $sql = "SELECT id FROM admin_permissions WHERE `key` = ? AND deleted_at IS NULL";
        $row = $this->fetch($sql, [$key]);
        return (int)($row['id'] ?? 0);
    }

    /**
     * 插入默认权限种子(三级层级:system → system.user/role/permission → 各 list/create/edit/delete)
     */
    public function seedDefaultPermissions(): void
    {
        $permissions = [
            ['系统管理', 'system', '系统管理模块', null],
            ['用户管理', 'system.user', '用户管理', 'system'],
            ['用户列表', 'system.user.list', '查看用户列表', 'system.user'],
            ['创建用户', 'system.user.create', '创建新用户', 'system.user'],
            ['编辑用户', 'system.user.edit', '编辑用户信息', 'system.user'],
            ['删除用户', 'system.user.delete', '删除用户', 'system.user'],
            ['角色管理', 'system.role', '角色管理', 'system'],
            ['角色列表', 'system.role.list', '查看角色列表', 'system.role'],
            ['创建角色', 'system.role.create', '创建新角色', 'system.role'],
            ['编辑角色', 'system.role.edit', '编辑角色信息', 'system.role'],
            ['删除角色', 'system.role.delete', '删除角色', 'system.role'],
            ['权限管理', 'system.permission', '权限管理', 'system'],
            ['权限列表', 'system.permission.list', '查看权限列表', 'system.permission'],
            ['创建权限', 'system.permission.create', '创建新权限', 'system.permission'],
            ['编辑权限', 'system.permission.edit', '编辑权限信息', 'system.permission'],
            ['删除权限', 'system.permission.delete', '删除权限', 'system.permission'],
        ];
        foreach ($permissions as $perm) {
            $parent_id = $perm[3] ? $this->getIdByKey($perm[3]) : 0;
            $this->create([
                'name' => $perm[0],
                'key' => $perm[1],
                'description' => $perm[2],
                'parent_id' => $parent_id,
                'sort_order' => 0,
            ]);
        }
    }

    /**
     * 给角色关联全部权限
     */
    public function grantAllPermissions(int $roleId): void
    {
        $sql = "SELECT id FROM admin_permissions WHERE deleted_at IS NULL";
        $rows = $this->fetchAll($sql);
        $ids = array_column($rows, 'id');
        $this->setRolePermissions($roleId, $ids);
    }
}
