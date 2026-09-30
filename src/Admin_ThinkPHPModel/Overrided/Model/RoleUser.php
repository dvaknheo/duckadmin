<?php declare(strict_types=1);

namespace DuckAdmin\Admin_ThinkPHPModel\Overrided\Model;

use think\Model;

class RoleUser extends Model
{
    protected $table = 'admin_role_users';
    protected $pk = 'id';
    
    protected $type = [
        'id' => 'integer',
        'user_id' => 'integer',
        'role_id' => 'integer',
    ];
    
    // 不自动维护时间戳
    protected $autoWriteTimestamp = false;
    
    // 关联：用户
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'user_id', 'id');
    }
    
    // 关联：角色
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }
}
