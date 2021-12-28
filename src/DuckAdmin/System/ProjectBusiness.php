<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\System;

use DuckPhp\Foundation\ThrowOnableTrait;
use DuckPhp\Helper\BusinessHelperTrait;
use DuckPhp\SingletonEx\SingletonExTrait;
/**
 * 工程动作基类，绑定了助手，和 ThrowOn()
 */
class ProjectBusiness
{
    use SingletonExTrait; // 单例
    use BusinessHelperTrait; //使用助手函数
    use ThrowOnableTrait;  //使用 ThrowOn()
    //protected $exception_class = ProjectException::class; 
}
