<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckUser\Controller;

use DuckUser\System\ProjectController;

// 基类，其他类都调用这个类，而不和 DuckUser\System 联系
class Base extends ProjectController
{
    public function __construct()
    {
        if(static::class === self::class){
            static::Exit404();
        }
    }
}