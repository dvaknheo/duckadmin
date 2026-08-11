<?php declare(strict_types=1);
/**
 * DuckPhp Admin System - Role Model
 */
namespace DuckAdmin\Admin\Model;

class RoleModel extends Base
{
    public function getAll(): array
    {
        $sql = "SELECT * FROM admin_roles WHERE deleted_at IS NULL ORDER BY pid ASC, id ASC";
        return $this->fetchAll($sql);
    }

    public function getById(int $id): ?array
    {
        $sql = "SELECT * FROM admin_roles WHERE id = ? AND deleted_at IS NULL";
        $ret = $this->fetch($sql, [$id]);
        return $ret === false ? null : $ret;
    }

    public function getPageList(int $page, int $pageSize, string $search = ''): array
    {
        $where = "deleted_at IS NULL";
        $params = [];
        if ($search !== '') {
            $where .= " AND name LIKE ?";
            $params[] = '%' . $search . '%';
        }
        $totalSql = "SELECT COUNT(*) as total FROM admin_roles WHERE {$where}";
        $total = $this->fetch($totalSql, $params)['total'] ?? 0;
        $offset = ($page - 1) * $pageSize;
        $listSql = "SELECT * FROM admin_roles WHERE {$where} ORDER BY pid ASC, id ASC LIMIT ? OFFSET ?";
        $listParams = array_merge($params, [$pageSize, $offset]);
        $list = $this->fetchAll($listSql, $listParams);
        return ['total' => (int)$total, 'list' => $list];
    }

    public function create(array $data): int
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        $sql = "INSERT INTO admin_roles (pid, name, description, is_super, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?)";
        $this->execute($sql, [
            $data['pid'] ?? 0, $data['name'], $data['description'] ?? '',
            $data['is_super'] ?? 0,
            $data['created_at'], $data['updated_at']
        ]);
        return (int)$this->lastInsertId();
    }

    public function edit(int $id, array $data): bool
    {
        $fields = [];
        $params = [];
        foreach (['pid', 'name', 'description', 'is_super'] as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = ?";
                $params[] = $data[$field];
            }
        }
        if (empty($fields)) {
            return false;
        }
        $params[] = date('Y-m-d H:i:s');
        $params[] = $id;
        $sql = "UPDATE admin_roles SET " . implode(', ', $fields) . ", updated_at = ? WHERE id = ?";
        $this->execute($sql, $params);
        return true;
    }

    public function delete(int $id): bool
    {
        $now = date('Y-m-d H:i:s');
        $sql = "UPDATE admin_roles SET deleted_at = ?, updated_at = ? WHERE id = ?";
        $this->execute($sql, [$now, $now, $id]);
        return true;
    }

    public function getUserRoleIds(int $userId): array
    {
        $sql = "SELECT role_id FROM admin_role_users WHERE user_id = ?";
        $rows = $this->fetchAll($sql, [$userId]);
        return array_column($rows, 'role_id');
    }

    public function setUserRoles(int $userId, array $roleIds): void
    {
        $sql = "DELETE FROM admin_role_users WHERE user_id = ?";
        $this->execute($sql, [$userId]);
        foreach ($roleIds as $roleId) {
            $sql = "INSERT INTO admin_role_users (user_id, role_id) VALUES (?, ?)";
            $this->execute($sql, [$userId, (int)$roleId]);
        }
    }

    /**
     * 插入根职位(超级管理员,pid=0),返回其 role_id
     */
    public function seedDefaultRoles(): int
    {
        return $this->create([
            'name' => '超级管理员',
            'description' => '拥有所有权限',
            'pid' => 0,
            'is_super' => 1,
        ]);
    }

    /**
     * 获取指定职位的整棵子树 id(含自身)
     */
    public function getSubTreeIds(int $pid): array
    {
        $all = $this->fetchAll("SELECT id, pid FROM admin_roles WHERE deleted_at IS NULL");
        $children = [];
        foreach ($all as $row) {
            $children[(int)$row['pid']][] = (int)$row['id'];
        }
        $ids = [];
        $stack = [$pid];
        while ($stack) {
            $id = (int)array_pop($stack);
            $ids[] = $id;
            foreach ($children[$id] ?? [] as $child) {
                $stack[] = $child;
            }
        }
        return $ids;
    }

    /**
     * 用户是否拥有超级管理员角色
     */
    public function isSuperRole(int $userId): bool
    {
        $sql = "SELECT r.id FROM admin_roles r
                INNER JOIN admin_role_users ru ON r.id = ru.role_id
                WHERE ru.user_id = ? AND r.deleted_at IS NULL AND r.is_super = 1";
        $row = $this->fetch($sql, [$userId]);
        return !empty($row);
    }

    /**
     * 全部职位 id
     */
    public function getAllIds(): array
    {
        $rows = $this->fetchAll("SELECT id FROM admin_roles WHERE deleted_at IS NULL");
        return array_column($rows, 'id');
    }
}

