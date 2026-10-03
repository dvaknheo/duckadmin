<?php declare(strict_types=1);

namespace DuckAdmin\Admin_ThinkPHPModel\Overrided\Model\Adapter;

use DuckAdmin\Admin\Model\PermissionModel as BasePermissionModel;
use DuckAdmin\Admin_ThinkPHPModel\Overrided\Model\Permission as ThinkPermission;

class PermissionModelAdapter extends BasePermissionModel
{
    public function getAll(): array
    {
        return ThinkPermission::whereNull('deleted_at')
            ->order('weight', 'asc')
            ->order('id', 'asc')
            ->select()
            ->toArray();
    }
    
    public function getById(int $id): ?array
    {
        $perm = ThinkPermission::whereNull('deleted_at')->find($id);
        return $perm ? $perm->toArray() : null;
    }
    
    public function getByUrl(string $url): ?array
    {
        $perm = ThinkPermission::whereNull('deleted_at')->where('url', $url)->find();
        return $perm ? $perm->toArray() : null;
    }
    
    public function getPageList(int $page, int $pageSize, string $search = ''): array
    {
        $query = ThinkPermission::whereNull('deleted_at');
        
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->whereOr('url', 'like', '%' . $search . '%');
            });
        }
        
        $total = $query->count();
        $list = $query->order('weight', 'asc')
            ->order('id', 'asc')
            ->page($page, $pageSize)
            ->select()
            ->toArray();
        
        return ['total' => $total, 'list' => $list];
    }
    
    public function create(array $data): int
    {
        $perm = ThinkPermission::create([
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
        $perm = ThinkPermission::find($id);
        if (!$perm) {
            return false;
        }
        
        return $perm->save([
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
        $perm = ThinkPermission::find($id);
        if (!$perm) {
            return false;
        }
        
        $perm->deleted_at = date('Y-m-d H:i:s');
        return $perm->save();
    }
    
    public function hasChildren(int $id): bool
    {
        return ThinkPermission::where('parent_id', $id)
            ->whereNull('deleted_at')
            ->count() > 0;
    }
    
    public function getDirIdByName(string $name): int
    {
        return $this->findDirectoryId($name, 0);
    }
    
    public function findDirectoryId(string $name, int $parentId): int
    {
        $perm = ThinkPermission::where('type', 0)
            ->where('name', $name)
            ->where('parent_id', $parentId)
            ->whereNull('deleted_at')
            ->find();
        return $perm ? (int)$perm->id : 0;
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
                    $id = $this->create([
                        'name' => (string)$node['name'],
                        'url' => $url,
                        'type' => $type,
                        'parent_id' => $parentId,
                        'weight' => 0,
                        'source' => 1,
                    ]);
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
        return ThinkPermission::whereNull('deleted_at')
            ->column('id');
    }
    
    public function getMenuItemsByRole(int $roleId): array
    {
        if (!$roleId) {
            return [];
        }

        // 注意:think-orm 没有 Eloquent 的 whereHas(),这里用显式 JOIN
        return ThinkPermission::alias('p')
            ->join('admin_role_permissions rp', 'p.id = rp.permission_id')
            ->where('rp.role_id', $roleId)
            ->whereNull('p.deleted_at')
            ->whereIn('p.type', [0, 1])
            ->field('p.id, p.name, p.url, p.type, p.parent_id, p.weight')
            ->order('p.weight', 'asc')
            ->order('p.id', 'asc')
            ->select()
            ->toArray();
    }
    
    public function getAllMenuItems(): array
    {
        return ThinkPermission::whereNull('deleted_at')
            ->whereIn('type', [0, 1])
            ->field('id, name, url, type, parent_id, weight')
            ->order('weight', 'asc')
            ->order('id', 'asc')
            ->select()
            ->toArray();
    }
    
    public function clean(): void
    {
        ThinkPermission::where('id', '>', 0)->delete();
    }
}
