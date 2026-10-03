<?php declare(strict_types=1);

namespace DuckAdmin\Admin_ThinkPHPModel\Overrided\Model\Adapter;

use DuckAdmin\Admin\Model\AdminModel as BaseAdminModel;
use DuckAdmin\Admin_ThinkPHPModel\Overrided\Model\Admin as ThinkAdmin;
use DuckAdmin\Admin_ThinkPHPModel\Overrided\Model\RoleUser as ThinkRoleUser;

class AdminModelAdapter extends BaseAdminModel
{
    public function getById(int $id): ?array
    {
        $admin = ThinkAdmin::whereNull('deleted_at')->find($id);
        return $admin ? $this->toArrayWithPassword($admin) : null;
    }
    
    public function getByUsername(string $username): ?array
    {
        $admin = ThinkAdmin::whereNull('deleted_at')
            ->where('username', $username)
            ->find();
        return $admin ? $this->toArrayWithPassword($admin) : null;
    }
    
    /**
     * 转换为数组，确保包含 password 字段（用于密码验证）
     */
    private function toArrayWithPassword($admin): array
    {
        $data = $admin->toArray();
        $data['password'] = $admin->password;
        return $data;
    }
    
    public function getPageList(int $page, int $pageSize, array $search = [], ?array $roleIds = null): array
    {
        $query = ThinkAdmin::whereNull('deleted_at');
        
        // 搜索条件
        foreach (['username', 'realname', 'email'] as $field) {
            if (!empty($search[$field])) {
                $query->where($field, 'like', '%' . $search[$field] . '%');
            }
        }
        
        // 角色过滤
        if ($roleIds !== null) {
            if (empty($roleIds)) {
                return ['total' => 0, 'list' => []];
            }
            // 注意:think-orm 没有 Eloquent 的 whereHas(),先取该角色下的用户 id 再过滤
            $userIds = ThinkRoleUser::whereIn('role_id', $roleIds)->column('user_id');
            if (empty($userIds)) {
                return ['total' => 0, 'list' => []];
            }
            $query->whereIn('id', $userIds);
        }
        
        $total = $query->count();
        $list = $query->order('id', 'asc')
            ->page($page, $pageSize)
            ->select()
            ->toArray();
        
        return ['total' => $total, 'list' => $list];
    }
    
    public function create(array $data): int
    {
        $admin = ThinkAdmin::create([
            'username' => $data['username'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'realname' => $data['realname'] ?? '',
            'email' => $data['email'] ?? '',
            'status' => $data['status'] ?? 1,
        ]);
        return $admin->id;
    }
    
    public function edit(int $id, array $data): bool
    {
        $admin = ThinkAdmin::find($id);
        if (!$admin) {
            return false;
        }
        
        $updateData = [];
        foreach (['username', 'realname', 'email', 'status'] as $field) {
            if (isset($data[$field])) {
                $updateData[$field] = $data[$field];
            }
        }
        if (isset($data['password']) && $data['password'] !== '') {
            $updateData['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        return $admin->save($updateData);
    }
    
    public function delete(int $id): bool
    {
        $admin = ThinkAdmin::find($id);
        if (!$admin) {
            return false;
        }
        
        // 软删除
        $admin->deleted_at = date('Y-m-d H:i:s');
        return $admin->save();
    }
    
    public function updateLoginTime(int $id): void
    {
        ThinkAdmin::where('id', $id)->update(['last_login_at' => date('Y-m-d H:i:s')]);
    }
}
