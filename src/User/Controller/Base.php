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
    public function mergeViewData($data)
    {
        $user_name = Helper::UserName();
        Helper::setViewHeadFoot('Home/inc-head','Home/inc-foot');
        Helper::assignViewData();
    }
    public function initController($class)
    {
        
        Helper::_()->checkCsrf();
        
        $csrf_token = Helper::_()->csrfToken();
        $csrf_field = Helper::_()->csrfField();
        

    }
}