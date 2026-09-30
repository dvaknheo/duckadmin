<?php declare(strict_types=1);

namespace DuckAdmin\Admin_LaravelModel\Overrided\Model\Adapter;

use DuckAdmin\Admin\Model\PermissionModel as BasePermissionModel;
use DuckAdmin\Admin_LaravelModel\Overrided\Model\Eloquent\Permission;

class PermissionModelAdapter extends BasePermissionModel
{
    public function getAll(): array
    {
        return Permission::active()
            ->orderBy('weight', 'asc')
            ->orderBy('id', 'asc')
            ->get()
            ->toArray();
    }
    
    public function getById(int $id): ?array
    {
        $perm = Permission::active()->find($id);
        return $perm ? $perm->toArray() : null;
    }
    
    public function getPageList(int $page, int $pageSize, string $search = ''): array
    {
        $query = Permission::active();
        
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('url', 'like', '%' . $search . '%');
            });
        }
        
        $total = $query->count();
        $list = $query->orderBy('weight', 'asc')
            ->orderBy('id', 'asc')
            ->offset(($page - 1) * $pageSize)
            ->limit($pageSize)
            ->get()
            ->toArray();
        
        return ['total' => $total, 'list' => $list];
    }
    
    public function create(array $data): int
    {
        $perm = Permission::create([
            'name' => $data['name'],
            'url' => $data['url'] ?? '',
            'type' => $data['type'] ?? 1,
            'parent_id' => $data['parent_id'] ?? 0,
            'weight' => $data['weight'] ?? 0,
            'source' => $data['source'] ?? 0,
        ]);
        return $perm->id;
    }
    
    public function edit(int $id, array $data): bool
    {
        $perm = Permission::find($id);
        if (!$perm) {
            return false;
        }
        
        return $perm->update([
            'name' => $data['name'],
            'url' => $data['url'] ?? '',
            'type' => $data['type'] ?? 1,
            'parent_id' => $data['parent_id'] ?? 0,
            'weight' => $data['weight'] ?? 0,
            'source' => $data['source'] ?? 0,
        ]);
    }
    
    public function delete(int $id): bool
    {
        $perm = Permission::find($id);
        if (!$perm) {
            return false;
        }
        
        $perm->deleted_at = now();
        return $perm->save();
    }
    
    public function hasChildren(int $id): bool
    {
        return Permission::active()
            ->where('parent_id', $id)
            ->exists();
    }
    
    public function findDirectoryId(string $name, int $parentId): int
    {
        $row = Permission::active()
            ->where('type', 0)
            ->where('name', $name)
            ->where('parent_id', $parentId)
            ->first();
        return $row ? $row->id : 0;
    }
    
    public function importMenu(array $menuTree): array
    {
        $existing = [];
        foreach ($this->getAll() as $p) {
            $existing[$p['url']] = (int)$p['id'];
        }
        
        $added = [];
        
        $import = function (array $nodes, int $parentId) use (&$import, &$existing, &$added) {
            foreach ($nodes as $node) {
                $url = (string)($node['url'] ?? '');
                $type = (int)($node['type'] ?? 1);
                
                if (isset($existing[$url])) {
                    $id = $existing[$url];
                } else {
                    $perm = Permission::create([
                        'name' => (string)$node['name'],
                        'url' => $url,
                        'type' => $type,
                        'parent_id' => $parentId,
                        'weight' => 0,
                        'source' => 1,
                    ]);
                    $id = $perm->id;
                    $existing[$url] = $id;
                    if ($type !== 0) {
                        $added[] = $url;
                    }
                }
                
                if (!empty($node['children'])) {
                    $import($node['children'], $id);
                }
            }
        };
        
        $import($menuTree, 0);
        return $added;
    }
    
    public function getAllIds(): array
    {
        return Permission::active()->pluck('id')->toArray();
    }
    
    public function getMenuItemsByRole(int $roleId): array
    {
        if (!$roleId) {
            return [];
        }
        
        return Permission::active()
            ->menus()
            ->whereHas('rolePermissions', function ($q) use ($roleId) {
                $q->where('role_id', $roleId);
            })
            ->orderBy('weight', 'asc')
            ->orderBy('id', 'asc')
            ->get()
            ->toArray();
    }
    
    public function getAllMenuItems(): array
    {
        return Permission::active()
            ->menus()
            ->orderBy('weight', 'asc')
            ->orderBy('id', 'asc')
            ->get()
            ->toArray();
    }
    
    public function clean(): void
    {
        Permission::query()->delete();
    }
}
