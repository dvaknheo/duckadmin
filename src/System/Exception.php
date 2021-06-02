<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\System;

use DuckPhp\ThrowOn\ThrowOnTrait;

/**
 * 这是系统的错误基类。
 */
class Exception extends \Exception
{
    use ThrowOnTrait;
}
