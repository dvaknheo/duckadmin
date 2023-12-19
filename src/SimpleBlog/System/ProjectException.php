<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace SimpleBlog\System;

use DuckPhp\Foundation\SimpleExceptionTrait;

/**
 * 这是工程的错误基类。 可以使用 ThrowOn
 */
class ProjectException extends \Exception
{
    use SimpleExceptionTrait;
}
