<?php declare(strict_types=1);

namespace DuckAdmin\Admin_LaravelModel\Overrided\Model\Eloquent;

class Admin extends Base
{
    protected $table = 'admin_admins';
    
    protected $fillable = [
        'username', 'password', 'realname', 'email', 'status'
    ];
    
    protected $hidden = ['password'];
    
    protected $casts = [
        'status' => 'integer',
        'last_login_at' => 'datetime',
    ];
    
    // 关联：一个管理员对应一个角色用户
    public function roleUser()
    {
        return $this->hasOne(RoleUser::class, 'user_id');
    }
    
    // 关联：通过 roleUser 获取角色
    public function role()
    {
        return $this->hasOneThrough(
            Role::class, 
            RoleUser::class, 
            'user_id',   // RoleUser 的外键
            'id',        // Role 的主键
            'id',        // Admin 的主键
            'role_id'    // RoleUser 关联到 Role 的外键
        );
    }
    
    // 作用域：未删除
    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }
}
