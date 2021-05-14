<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\App;

use DuckPhp\ThrowOn\ThrowOnTrait;
use DuckAdmin\App\App;

class BaseException extends \Exception
{
    use ThrowOnTrait;
}
