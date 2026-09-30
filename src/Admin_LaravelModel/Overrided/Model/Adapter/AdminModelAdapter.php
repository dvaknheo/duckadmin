<?php declare(strict_types=1);

namespace DuckAdmin\Admin_LaravelModel\Overrided\Model\Adapter;

use DuckAdmin\Admin\Model\AdminModel as BaseAdminModel;
use DuckAdmin\Admin_LaravelModel\Overrided\Model\Eloquent\Admin as EloquentAdmin;

class AdminModelAdapter extends BaseAdminModel
{
    public function getById(int $id): ?array
    {
        $admin = EloquentAdmin::active()->find($id);
        return $admin ? $this->toArrayWithPassword($admin) : null;
    }
    
    public function getByUsername(string $username): ?array
    {
        $admin = EloquentAdmin::active()
            ->where('username', $username)
            ->first();
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
        $query = EloquentAdmin::active();
        
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
            $query->whereHas('roleUser', function ($q) use ($roleIds) {
                $q->whereIn('role_id', $roleIds);
            });
        }
        
        $total = $query->count();
        $list = $query->orderBy('id', 'asc')
            ->offset(($page - 1) * $pageSize)
            ->limit($pageSize)
            ->get()
            ->toArray();
        
        return ['total' => $total, 'list' => $list];
    }
    
    public function create(array $data): int
    {
        $admin = EloquentAdmin::create([
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
        $admin = EloquentAdmin::find($id);
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
        
        return $admin->update($updateData);
    }
    
    public function delete(int $id): bool
    {
        $admin = EloquentAdmin::find($id);
        if (!$admin) {
            return false;
        }
        
        // 软删除
        $admin->deleted_at = date('Y-m-d H:i:s');
        return $admin->save();
    }
    
    public function updateLoginTime(int $id): void
    {
        EloquentAdmin::where('id', $id)->update(['last_login_at' => date('Y-m-d H:i:s')]);
    }
}
