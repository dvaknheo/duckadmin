<?php declare(strict_types=1);
/**
 * DuckPhp Admin System - Business Base
 */
namespace DuckAdmin\Admin\Business;

use DuckPhp\Foundation\SingletonTrait;

use DuckAdmin\Admin\Controller\Session;

class Base
{
    use SingletonTrait;
    public function getCurrentAdminId()
    {
        return (int)Session::_()->getCurrentAdminId();
    }
}
