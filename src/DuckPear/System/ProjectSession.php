<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckAdmin\System;

use DuckPhp\Foundation\Session;
use DuckPhp\Foundation\ThrowOnableTrait;

/*
 * 工程的 Session
 */
class ProjectSession extends Session
{
    use ThrowOnableTrait;
}
