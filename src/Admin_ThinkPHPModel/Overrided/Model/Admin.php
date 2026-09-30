<?php declare(strict_types=1);

namespace DuckAdmin\Admin_ThinkPHPModel\Overrided\Model;

use think\Model;

class Admin extends Model
{
    protected $table = 'admin_admins';
    protected $pk = 'id';
    
    // 隐藏字段
    protected $hidden = ['password'];
    
    // 类型转换
    protected $type = [
        'id' => 'integer',
        'status' => 'integer',
    ];
    
    // 时间字段
    protected $autoWriteTimestamp = true;
    protected $createTime = 'created_at';
    protected $updateTime = 'updated_at';
    
    // 软删除字段
    protected $deleteTime = 'deleted_at';
    protected $defaultSoftDelete = null;
    
    // 关联：一个管理员对应一个角色用户
    public function roleUser()
    {
        return $this->hasOne(RoleUser::class, 'user_id', 'id');
    }
    
    // 作用域：未删除
    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }
}
