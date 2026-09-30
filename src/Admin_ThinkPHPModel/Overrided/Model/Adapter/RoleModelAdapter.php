<?php declare(strict_types=1);

namespace DuckAdmin\Admin_ThinkPHPModel\Overrided\Model\Adapter;

use DuckAdmin\Admin\Model\RoleModel as BaseRoleModel;
use DuckAdmin\Admin_ThinkPHPModel\Overrided\Model\Role as ThinkRole;

class RoleModelAdapter extends BaseRoleModel
{
    public function getAll(): array
    {
        return ThinkRole::whereNull('deleted_at')
            ->order('pid', 'asc')
            ->order('id', 'asc')
            ->select()
            ->toArray();
    }
    
    public function getById(int $id): ?array
    {
        $role = ThinkRole::whereNull('deleted_at')->find($id);
        return $role ? $role->toArray() : null;
    }
    
    public function getPageList(int $page, int $pageSize, string $search = ''): array
    {
        $query = ThinkRole::whereNull('deleted_at');
        
        if ($search !== '') {
            $query->where('name', 'like', '%' . $search . '%');
        }
        
        $total = $query->count();
        $list = $query->order('pid', 'asc')
            ->order('id', 'asc')
            ->page($page, $pageSize)
            ->select()
            ->toArray();
        
        return ['total' => $total, 'list' => $list];
    }
    
    public function create(array $data): int
    {
        $role = ThinkRole::create([
            'pid' => $data['pid'] ?? 0,
            'name' => $data['name'],
            'description' => $data['description'] ?? '',
            'is_super' => $data['is_super'] ?? 0,
        ]);
        return $role->id;
    }
    
    public function edit(int $id, array $data): bool
    {
        $role = ThinkRole::find($id);
        if (!$role) {
            return false;
        }
        
        $updateData = [];
        foreach (['pid', 'name', 'description', 'is_super'] as $field) {
            if (isset($data[$field])) {
                $updateData[$field] = $data[$field];
            }
        }
        
        if (empty($updateData)) {
            return false;
        }
        
        return $role->save($updateData);
    }
    
    public function delete(int $id): bool
    {
        $role = ThinkRole::find($id);
        if (!$role) {
            return false;
        }
        
        $role->deleted_at = date('Y-m-d H:i:s');
        return $role->save();
    }
    
    public function seedDefaultRoles(): int
    {
        return $this->create([
            'name' => '超级管理员',
            'description' => '拥有所有权限',
            'pid' => 0,
            'is_super' => 1,
        ]);
    }
    
    public function getSubTreeIds(int $pid): array
    {
        $all = ThinkRole::whereNull('deleted_at')
            ->field('id, pid')
            ->select()
            ->toArray();
        
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
    
    public function getAllIds(): array
    {
        return ThinkRole::whereNull('deleted_at')
            ->column('id');
    }
    
    public function isSuper(int $roleId): bool
    {
        return (bool)ThinkRole::where('id', $roleId)
            ->where('is_super', 1)
            ->find();
    }
}
