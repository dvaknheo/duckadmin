<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\User\Controller;
use DuckPhp\Foundation\ControllerTrait;

class Base
{
    use ControllerTrait;
    public function __construct()
    {
        $this->initController(static::class);
    }
    public function initController($class)
    {
        Helper::checkInstall('install');
        Helper::User()->checkAccess(null, null,null);
        Helper::setViewHeadFoot('_sys/inc-head','_sys/inc-foot');
        
        $csrf_token = Helper::_()->csrfToken();
        $csrf_field = Helper::_()->csrfField();
    }
}