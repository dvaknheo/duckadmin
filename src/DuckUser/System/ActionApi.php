<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckUser\Api;

use DuckPhp\SingletonEx\SingletonExTrait;
use DuckUser\Controller\UserBusiness;

class ActionApi
{
    use SingletonExTrait;
    public  function getCurrentUser()
    {
        return SessionManager::G()->getCurrentUser();
    }
    public function login($form)
    {
        //return 
    }
    public function logout()
    {
        //
    }
}