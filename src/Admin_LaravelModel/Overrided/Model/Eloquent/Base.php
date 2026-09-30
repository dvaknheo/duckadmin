<?php declare(strict_types=1);

namespace DuckAdmin\Admin_LaravelModel\Overrided\Model\Eloquent;

use Illuminate\Database\Eloquent\Model;

abstract class Base extends Model
{
    // 使用 created_at, updated_at
    public $timestamps = true;
    
    // 允许批量赋值所有字段
    protected $guarded = [];
    
    // 默认软删除
    protected $dates = ['deleted_at'];
}
