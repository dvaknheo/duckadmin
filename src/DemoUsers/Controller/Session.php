<?php declare(strict_types=1);
/**
 * DuckAdmin DemoUsers - Session 管理(极简)
 */
namespace DuckAdmin\DemoUsers\Controller;

use DuckPhp\Foundation\Controller\SessionTrait;
use DuckPhp\GlobalUser\UserSessionInterface;
use DuckPhp\GlobalUser\UserSessionTrait;

class Session implements UserSessionInterface
{
    use SessionTrait;
    use UserSessionTrait;
}
