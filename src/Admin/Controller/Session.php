<?php declare(strict_types=1);
/**
 * DuckPhp Admin System - Session 管理
 */
namespace DuckAdmin\Admin\Controller;

use DuckPhp\Foundation\Controller\SessionTrait;
use DuckPhp\GlobalAdmin\AdminSessionInterface;
use DuckPhp\GlobalAdmin\AdminSessionTrait;

class Session implements AdminSessionInterface
{
    use SessionTrait;
    use AdminSessionTrait;
}
