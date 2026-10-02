<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\User\Controller;

use DuckPhp\Foundation\Controller\ControllerHelper;
class Helper extends ControllerHelper
{
    public function csrfToken()
    {
        return Session::_()->csrfToken();
    }
    public function csrfField()
    {
        return Session::_()->csrfField();
    }
    
    public function checkCsrf()
    {
    }
}