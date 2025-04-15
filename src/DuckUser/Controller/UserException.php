<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckUser\Controller;

use DuckPhp\Foundation\SimpleExceptionTrait;
use DuckUser\System\ProjectException;

class UserException extends ProjectException
{
    use SimpleExceptionTrait;
}