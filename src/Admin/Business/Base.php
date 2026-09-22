<?php declare(strict_types=1);
/**
 * DuckPhp Admin System - Business Base
 */
namespace DuckAdmin\Admin\Business;

use DuckPhp\Foundation\Business\Base as BusinessBase;
use DuckAdmin\Admin\Controller\Session;

class Base extends BusinessBase
{
    public function getCurrentAdminId()
    {
        return (int)Session::_()->getCurrentAdminId();
    }
}
