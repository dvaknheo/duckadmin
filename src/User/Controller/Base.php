<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\User\Controller;
use DuckPhp\Foundation\SingletonTrait;
use DuckPhp\Foundation\Controller\UserControllerBase;

class Base extends UserControllerBase
{
    protected function initController()
    {
        
        //Helper::checkInstall('install');
        //$flag = Helper::User()->canAccess(null, null,null);
        //Helper::ControllerThrowOn(!$flag,"cannot login");
        
        parent::initController();
        Helper::setViewHeadFoot('_sys/inc-head','_sys/inc-foot');
        
        $csrf_token = Helper::_()->csrfToken();
        $csrf_field = Helper::_()->csrfField();
    }
}