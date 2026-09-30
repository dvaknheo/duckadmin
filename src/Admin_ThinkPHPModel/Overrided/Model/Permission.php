<?php declare(strict_types=1);

namespace DuckAdmin\Admin_ThinkPHPModel\Overrided\Model;

use think\Model;

class Permission extends Model
{
    protected $table = 'admin_permissions';
    protected $pk = 'id';
    
    protected $type = [
        'id' => 'integer',
        'parent_id' => 'integer',
        'type' => 'integer',
        'weight' => 'integer',
    ];
    
    protected $autoWriteTimestamp = true;
    protected $createTime = 'created_at';
    protected $updateTime = 'updated_at';
    
    // 关联：子权限
    public function children()
    {
        return $this->hasMany(Permission::class, 'parent_id', 'id');
    }
    
    // 关联：父权限
    public function parent()
    {
        return $this->belongsTo(Permission::class, 'parent_id', 'id');
    }
    
    // 关联：角色的权限关联
    public function rolePermissions()
    {
        return $this->hasMany(RolePermission::class, 'permission_id', 'id');
    }
    
    // 作用域：未删除
    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }
    
    // 作用域：菜单类型
    public function scopeMenus($query)
    {
        return $query->whereIn('type', [0, 1]);
    }
}
