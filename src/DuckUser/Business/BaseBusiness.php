<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckUser\Business;

use DuckPhp\Foundation\ThrowOnableTrait;
use DuckPhp\Helper\BusinessHelperTrait;
use DuckPhp\SingletonEx\SingletonExTrait;

class BaseBusiness
{
    //其他 Business 类，都调用这个类，而不和 DuckUser\System 发生联系
    
    use SingletonExTrait;
    use BusinessHelperTrait;
    use ThrowOnableTrait;
    public function __construct()
    {
        // 绑定 ThrowOn 的异常类
        $this->exception_class = $this->exception_class ?? ProjectException::class;
    }
}
