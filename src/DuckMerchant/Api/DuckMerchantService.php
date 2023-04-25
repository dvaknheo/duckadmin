<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckMerchant\Api;

use DuckPhp\SingletonEx\SingletonExTrait;

class DuckMerchantService
{
    use SingletonExTrait;
    
    public function register($form)
    {
        return UserBusiness::G()->register($form);
    }
    public function login($form)
    {
        return UserBusiness::G()->login($form);
    }
    public function changePassword($uid, $password, $new_password)
    {
        return UserBusiness::G()->changePassword($uid, $password, $new_password);
    }
}