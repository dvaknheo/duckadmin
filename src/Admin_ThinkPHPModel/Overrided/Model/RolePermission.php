<?php declare(strict_types=1);

namespace DuckAdmin\Admin_ThinkPHPModel\Overrided\Model;

use think\Model;

class RolePermission extends Model
{
    protected $table = 'admin_role_permissions';
    protected $pk = 'id';
    
    protected $type = [
        'id' => 'integer',
        'role_id' => 'integer',
        'permission_id' => 'integer',
    ];
    
    // 不自动维护时间戳
    protected $autoWriteTimestamp = false;
    
    // 关联：角色
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }
    
    // 关联：权限
    public function permission()
    {
        return $this->belongsTo(Permission::class, 'permission_id', 'id');
    }
}
