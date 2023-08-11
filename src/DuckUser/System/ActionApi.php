<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckUser\Api;

use DuckPhp\SingletonEx\SingletonExTrait;

class DuckUserAction
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