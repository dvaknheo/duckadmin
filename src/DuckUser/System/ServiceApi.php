<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckUser\System;

use DuckPhp\Foundation\SimpleBusinessTrait;
use DuckUser\Business\UserBusiness;

class ServiceAPi
{
    use SimpleBusinessTrait;
    
    public function register($form)
    {
        return UserBusiness::_()->register($form);
    }
    public function login($form)
    {
        return UserBusiness::_()->login($form);
    }
    public function changePassword($uid, $password, $new_password)
    {
        return UserBusiness::_()->changePassword($uid, $password, $new_password);
    }
}