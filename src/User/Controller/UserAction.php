<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckAdmin\User\Controller;

use DuckPhp\GlobalUser\UserActionInterface;
use DuckPhp\Core\App;

use DuckAdmin\User\Business\UserBusiness;

class UserAction extends Base
{
    public function __construct()
    {
        // just override for skip init;
    }
    public function service()
    {
        return UserBusiness::_();
    }
    public function session()
    {
        return Session::_();
    }

}