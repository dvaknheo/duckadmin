<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace SimpleBlog\Business;

use DuckPhp\Foundation\SimpleExceptionTrait;

class BusinessException extends ProjectException
{
    use SimpleExceptionTrait;
}
