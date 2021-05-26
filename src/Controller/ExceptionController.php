<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Controller;

/**
 * 这个类我们准备用来集中处理各种异常情况。
 */
class ExceptionController
{
    public function __construct()
    {
        if (static::class === self::class) {
            //禁止直接访问
            C::Exit404();
        }
        $this->initialize();
    }

}