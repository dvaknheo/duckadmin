<?php declare(strict_types=1);

namespace DuckAdmin\Admin_ThinkPHPModel\Overrided\Model;

use think\Model;

class Role extends Model
{
    protected $table = 'admin_roles';
    protected $pk = 'id';
    
    protected $type = [
        'id' => 'integer',
        'pid' => 'integer',
        'is_super' => 'integer',
    ];
    
    protected $autoWriteTimestamp = true;
    protected $createTime = 'created_at';
    protected $updateTime = 'updated_at';
    
    // 关联：角色下的用户关联
    public function roleUsers()
    {
        return $this->hasMany(RoleUser::class, 'role_id', 'id');
    }
    
    // 关联：角色的权限关联
    public function rolePermissions()
    {
        return $this->hasMany(RolePermission::class, 'role_id', 'id');
    }
    
    // 作用域：未删除
    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }
}
