<?php declare(strict_types=1);
/**
 * DuckPhp Admin System - Admin Model
 * 管理员模型
 */
namespace DuckAdmin\Admin\Model;

class AdminModel extends Base
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

    public function getPageList(int $page, int $pageSize, array $search = []): array
    {
        $where = "deleted_at IS NULL";
        $params = [];
        foreach (['username', 'realname', 'email'] as $field) {
            if (!empty($search[$field])) {
                $where .= " AND {$field} LIKE ?";
                $params[] = '%' . $search[$field] . '%';
            }
        }
        $totalSql = "SELECT COUNT(*) as total FROM admin_admins WHERE {$where}";
        $totalRow = $this->fetch($totalSql, $params);
        $total = $totalRow['total'] ?? 0;
        $offset = ($page - 1) * $pageSize;
        $listSql = "SELECT * FROM admin_admins WHERE {$where} ORDER BY id ASC LIMIT ? OFFSET ?";
        $list = $this->fetchAll($listSql, array_merge($params, [$pageSize, $offset]));
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
