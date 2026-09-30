<?php declare(strict_types=1);

namespace DuckAdmin\Admin_LaravelModel\Overrided\Model\Eloquent;

class RolePermission extends Base
{
    protected $table = 'admin_role_permissions';
    
    // 关联表通常不需要时间戳
    public $timestamps = false;
    
    protected $fillable = ['role_id', 'permission_id'];
    
    protected $casts = [
        'role_id' => 'integer',
        'permission_id' => 'integer',
    ];
    
    // 关联：角色
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
    
    // 关联：权限
    public function permission()
    {
        return $this->belongsTo(Permission::class, 'permission_id');
    }
}
