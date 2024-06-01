<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckUser\Controller;
use DuckPhp\Foundation\SimpleControllerTrait;

class Base
{
    use SimpleControllerTrait;
    public function __construct()
    {
        $this->initController(static::class);
    }
    public function initController($class)
    {
        
        Helper::_()->checkCsrf();
        
        $csrf_token = Helper::_()->csrfToken();
        $csrf_field = Helper::_()->csrfField();
        
        $user_name = Helper::UserName();
        Helper::setViewHeadFoot('home/inc-head','home/inc-foot');
        Helper::assignViewData(get_defined_vars());
    }
}