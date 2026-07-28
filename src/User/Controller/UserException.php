<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\User\Controller;

use DuckPhp\Foundation\ExceptionTrait;
use DuckAdmin\User\System\ProjectException;

class UserException extends ProjectException
{
    use ExceptionTrait;
}