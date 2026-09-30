<?php declare(strict_types=1);

namespace DuckAdmin\Admin_LaravelModel\Overrided\Model\Eloquent;

class Role extends Base
{
    protected $table = 'admin_roles';
    
    protected $fillable = [
        'pid', 'name', 'description', 'is_super'
    ];
    
    protected $casts = [
        'pid' => 'integer',
        'is_super' => 'boolean',
    ];
    
    // 关联：一个角色有多个用户
    public function roleUsers()
    {
        return $this->hasMany(RoleUser::class, 'role_id');
    }
    
    // 关联：一个角色有多个权限
    public function rolePermissions()
    {
        return $this->hasMany(RolePermission::class, 'role_id');
    }
    
    // 作用域：未删除
    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }
}
