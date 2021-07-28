<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckAdmin\System;

use DuckPhp\Foundation\SessionManagerBase;
use DuckPhp\Foundation\ThrowOnableTrait;

class ProjectSession extends SessionManagerBase
{
    use ThrowOnableTrait;
}
