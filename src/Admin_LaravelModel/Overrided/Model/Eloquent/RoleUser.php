<?php declare(strict_types=1);

namespace DuckAdmin\Admin_LaravelModel\Overrided\Model\Eloquent;

class RoleUser extends Base
{
    protected $table = 'admin_role_users';
    
    // 关联表通常不需要时间戳
    public $timestamps = false;
    
    protected $fillable = ['user_id', 'role_id'];
    
    protected $casts = [
        'user_id' => 'integer',
        'role_id' => 'integer',
    ];
    
    // 关联：管理员
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'user_id');
    }
    
    // 关联：角色
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
}
