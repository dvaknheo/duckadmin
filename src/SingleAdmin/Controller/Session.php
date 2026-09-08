<?php declare(strict_types=1);
/**
 * DuckAdmin SingleAdmin - Session 管理(极简)
 */
namespace DuckAdmin\SingleAdmin\Controller;

use DuckPhp\Foundation\Controller\AdminSessionTrait;
use DuckPhp\Foundation\SessionTrait;

class Session
{
    use SessionTrait;
    use AdminSessionTrait;
}
