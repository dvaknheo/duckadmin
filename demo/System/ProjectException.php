<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckAdminDemo\System;

use DuckPhp\ThrowOn\ThrowOnTrait;

class ProjectException extends \Exception
{
    use ThrowOnTrait;
}
