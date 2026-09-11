<?php declare(strict_types=1);
/**
 * DuckPhp Admin System - Admin User Model
 */
namespace DuckAdmin\Admin\Model;

class AdminUserModel extends Base
{
    public function getById(int $id): ?array
    {
        $sql = "SELECT * FROM admin_admins WHERE id = ? AND deleted_at IS NULL";
        $ret = $this->fetch($sql, [$id]);
        return $ret === false ? null : $ret;
    }

    public function getByUsername(string $username): ?array
    {
        $sql = "SELECT * FROM admin_admins WHERE username = ? AND deleted_at IS NULL";
        $ret = $this->fetch($sql, [$username]);
        return $ret === false ? null : $ret;
    }

    public function getPageList(int $page, int $pageSize, string $search = '', ?array $roleIds = null): array
    {
        $where = "u.deleted_at IS NULL";
        $params = [];
        if ($search !== '') {
            $where .= " AND (u.username LIKE ? OR u.realname LIKE ? OR u.email LIKE ?)";
            $like = '%' . $search . '%';
            $params = [$like, $like, $like];
        }
        if ($roleIds !== null && !empty($roleIds)) {
            $in = implode(',', array_map('intval', $roleIds));
            $where .= " AND u.id IN (SELECT user_id FROM admin_role_users WHERE role_id IN ({$in}))";
        }
        $totalSql = "SELECT COUNT(*) as total FROM admin_admins u WHERE {$where}";
        $totalRow = $this->fetch($totalSql, $params);
        $total = $totalRow['total'] ?? 0;
        $offset = ($page - 1) * $pageSize;
        $listSql = "SELECT u.id, u.username, u.realname, u.email, u.status, u.last_login_at, u.created_at, u.updated_at,
                           GROUP_CONCAT(r.name) AS role_names
                    FROM admin_admins u
                    LEFT JOIN admin_role_users ru ON u.id = ru.user_id
                    LEFT JOIN admin_roles r ON ru.role_id = r.id AND r.deleted_at IS NULL
                    WHERE {$where}
                    GROUP BY u.id
                    ORDER BY u.id ASC LIMIT ? OFFSET ?";
        $listParams = array_merge($params, [$pageSize, $offset]);
        $list = $this->fetchAll($listSql, $listParams);
        return ['total' => (int)$total, 'list' => $list];
    }

    public function create(array $data): bool
    {
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        $sql = "INSERT INTO admin_admins (username, password, realname, email, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $this->execute($sql, [
            $data['username'], $data['password'], $data['realname'],
            $data['email'], $data['status'] ?? 1,
            $data['created_at'], $data['updated_at']
        ]);
        return true;
    }

    public function edit(int $id, array $data): bool
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $fields = [];
        $params = [];
        foreach (['username', 'realname', 'email', 'status', 'updated_at'] as $field) {
            if (isset($data[$field])) {
                $fields[] = "{$field} = ?";
                $params[] = $data[$field];
            }
        }
        if (isset($data['password']) && $data['password'] !== '') {
            $fields[] = "password = ?";
            $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        if (empty($fields)) {
            return false;
        }
        $params[] = $id;
        $sql = "UPDATE admin_admins SET " . implode(', ', $fields) . " WHERE id = ?";
        $this->execute($sql, $params);
        return true;
    }

    public function delete(int $id): bool
    {
        $sql = "UPDATE admin_admins SET deleted_at = ?, updated_at = ? WHERE id = ?";
        $now = date('Y-m-d H:i:s');
        $this->execute($sql, [$now, $now, $id]);
        return true;
    }

    public function updateLoginTime(int $id): void
    {
        $sql = "UPDATE admin_admins SET last_login_at = ? WHERE id = ?";
        $this->execute($sql, [date('Y-m-d H:i:s'), $id]);
    }
}
