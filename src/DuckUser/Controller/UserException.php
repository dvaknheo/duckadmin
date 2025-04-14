<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckUser\Controller;

use DuckPhp\Foundation\SimpleExceptionTrait;
use Exception;

class UserException extends Exception;
{
    use SimpleExceptionTrait;
}