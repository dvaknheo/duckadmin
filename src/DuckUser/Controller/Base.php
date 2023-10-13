<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckUser\Controller;
use DuckPhp\Foundation\SimpleControllerTrait;
use DuckUser\System\ProjectException;

class Base
{
    use SimpleControllerTrait;
    public function __construct()
    {
        $this->initController(static::class);
    }
    public function initController($class)
    {
        Helper::assignExceptionHandler(ProjectException::class, [ExceptionReporter::class, 'OnException']);
        
        Helper::_()->checkCsrf();
        
        $csrf_token = Helper::_()->csrfToken();
        $csrf_field = Helper::_()->csrfField();
         
        $user = UserAction::_()->data();
        $user_name = $user['username'] ?? '';
        Helper::setViewHeadFoot('home/inc-head','home/inc-foot');
        Helper::assignViewData(get_defined_vars());
    }
}