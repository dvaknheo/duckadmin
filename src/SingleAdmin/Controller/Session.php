<?php declare(strict_types=1);
/**
 * DuckAdmin SingleAdmin - Session 管理(极简)
 */
namespace DuckAdmin\SingleAdmin\Controller;

use DuckPhp\Foundation\Controller\SessionTrait;
use DuckPhp\GlobalAdmin\AdminSessionTrait;

class Session
{
    use SessionTrait;
    use AdminSessionTrait;
}
