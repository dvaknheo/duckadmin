<?php declare(strict_types=1);

namespace DuckAdmin\Admin_LaravelModel\Overrided\Model\Eloquent;

class Permission extends Base
{
    protected $table = 'admin_permissions';
    
    protected $fillable = [
        'name', 'url', 'type', 'parent_id', 'weight', 'source'
    ];
    
    protected $casts = [
        'type' => 'integer',
        'parent_id' => 'integer',
        'weight' => 'integer',
        'source' => 'integer',
    ];
    
    // 关联：子权限
    public function children()
    {
        return $this->hasMany(Permission::class, 'parent_id');
    }
    
    // 关联：父权限
    public function parent()
    {
        return $this->belongsTo(Permission::class, 'parent_id');
    }
    
    // 关联：角色的权限
    public function rolePermissions()
    {
        return $this->hasMany(RolePermission::class, 'permission_id');
    }
    
    // 作用域：未删除
    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }
    
    // 作用域：菜单类型（0=目录，1=菜单）
    public function scopeMenus($query)
    {
        return $query->whereIn('type', [0, 1]);
    }
}
