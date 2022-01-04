<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckUser\System;

use DuckPhp\Foundation\ThrowOnableTrait;
use DuckPhp\Helper\BusinessHelperTrait;
use DuckPhp\SingletonEx\SingletonExTrait;

class ProjectBusiness
{
    use SingletonExTrait;
    use BusinessHelperTrait;
    use ThrowOnableTrait;
    public function __construct()
    {
        // 绑定 ThrowOn 的异常类
        $this->exception_class = $this->exception_class ?? ProjectException::class;
    }
}
