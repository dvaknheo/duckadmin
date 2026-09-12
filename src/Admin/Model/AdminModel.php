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

    /**
     * 获取分页列表，可按角色组过滤（只显示指定角色组及其子孙组的用户）
     * 使用递归 CTE，兼容 sqlite 和 pgsql
     * @param array|null $roleIds 角色组 id 列表，null 表示不限制
     */
    public function getPageList(int $page, int $pageSize, array $search = [], ?array $roleIds = null): array
    {
        $where = "a.deleted_at IS NULL";
        $params = [];
        foreach (['username', 'realname', 'email'] as $field) {
            if (!empty($search[$field])) {
                $where .= " AND a.{$field} LIKE ?";
                $params[] = '%' . $search[$field] . '%';
            }
        }

        // 角色组过滤（递归 CTE 查询子孙组）
        $roleCte = '';
        if ($roleIds !== null) {
            if (empty($roleIds)) {
                return ['total' => 0, 'list' => []];
            }
            $roleList = implode(',', array_map('intval', $roleIds));
            $roleCte = "WITH RECURSIVE role_tree AS (
                SELECT id FROM admin_roles WHERE id IN ({$roleList}) AND deleted_at IS NULL
                UNION
                SELECT r.id FROM admin_roles r INNER JOIN role_tree rt ON r.pid = rt.id WHERE r.deleted_at IS NULL
            )
            ";
            $where .= " AND a.id IN (SELECT user_id FROM admin_role_users WHERE role_id IN (SELECT id FROM role_tree))";
        }

        // 统计总数
        $totalSql = "{$roleCte}SELECT COUNT(*) as total FROM admin_admins a WHERE {$where}";
        $totalRow = $this->fetch($totalSql, $params);
        $total = $totalRow['total'] ?? 0;

        // 查询列表
        $offset = ($page - 1) * $pageSize;
        $listSql = "{$roleCte}SELECT a.* FROM admin_admins a WHERE {$where} ORDER BY a.id ASC LIMIT ? OFFSET ?";
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
