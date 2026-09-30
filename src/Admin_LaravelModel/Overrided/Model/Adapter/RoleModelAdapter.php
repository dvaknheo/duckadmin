<?php declare(strict_types=1);

namespace DuckAdmin\Admin_LaravelModel\Overrided\Model\Adapter;

use DuckAdmin\Admin\Model\RoleModel as BaseRoleModel;
use DuckAdmin\Admin_LaravelModel\Overrided\Model\Eloquent\Role;

class RoleModelAdapter extends BaseRoleModel
{
    public function getAll(): array
    {
        return Role::active()
            ->orderBy('pid', 'asc')
            ->orderBy('id', 'asc')
            ->get()
            ->toArray();
    }
    
    public function getById(int $id): ?array
    {
        $role = Role::active()->find($id);
        return $role ? $role->toArray() : null;
    }
    
    public function getPageList(int $page, int $pageSize, string $search = ''): array
    {
        $query = Role::active();
        
        if ($search !== '') {
            $query->where('name', 'like', '%' . $search . '%');
        }
        
        $total = $query->count();
        $list = $query->orderBy('pid', 'asc')
            ->orderBy('id', 'asc')
            ->offset(($page - 1) * $pageSize)
            ->limit($pageSize)
            ->get()
            ->toArray();
        
        return ['total' => $total, 'list' => $list];
    }
    
    public function create(array $data): int
    {
        $role = Role::create([
            'pid' => $data['pid'] ?? 0,
            'name' => $data['name'],
            'description' => $data['description'] ?? '',
            'is_super' => $data['is_super'] ?? 0,
        ]);
        return $role->id;
    }
    
    public function edit(int $id, array $data): bool
    {
        $role = Role::find($id);
        if (!$role) {
            return false;
        }
        
        $updateData = [];
        foreach (['pid', 'name', 'description', 'is_super'] as $field) {
            if (isset($data[$field])) {
                $updateData[$field] = $data[$field];
            }
        }
        
        return $role->update($updateData);
    }
    
    public function delete(int $id): bool
    {
        $role = Role::find($id);
        if (!$role) {
            return false;
        }
        
        $role->deleted_at = now();
        return $role->save();
    }
    
    public function seedDefaultRoles(): int
    {
        $role = Role::create([
            'name' => '超级管理员',
            'description' => '拥有所有权限',
            'pid' => 0,
            'is_super' => 1,
        ]);
        return $role->id;
    }
    
    public function getSubTreeIds(int $pid): array
    {
        $all = Role::active()->get(['id', 'pid']);
        $children = [];
        foreach ($all as $row) {
            $children[$row->pid][] = $row->id;
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
    
    public function getAllIds(): array
    {
        return Role::active()->pluck('id')->toArray();
    }
    
    public function isSuper(int $roleId): bool
    {
        return Role::where('id', $roleId)
            ->where('is_super', 1)
            ->exists();
    }
}
