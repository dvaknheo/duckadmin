<?php declare(strict_types=1);
/**
 * DuckPhp Admin System
 */
namespace DuckAdmin\Admin\Controller;

use DuckPhp\Foundation\Controller\ControllerHelper;

class Helper extends ControllerHelper
{
    // 管理员事件
    public const EVENT_ADMIN_LOGINING = 'event_admin_logining';
    public const EVENT_ADMIN_LOGED = 'event_admin_loged';
    public const EVENT_ADMIN_LOGOUTING = 'event_admin_logouting';
    public const EVENT_ADMIN_LOGOUTED = 'event_admin_logouted';
}
