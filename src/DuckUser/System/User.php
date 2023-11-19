<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */
namespace DuckUser\System;

use DuckPhp\Component\GloalUser;

class User extends UserSystem
{
    public function current()
    {
        //return UserAction::CallInPhase(App::Phase());
    }
    public function id()
    {
        return UserAction::_()->current()->id();
    }
    public function data()
    {
        return UserAction::_()->current()->data();
    }
    protected function checkLogin()
    {
        return UserAction::_()->current();
    }
    ///////////////
    public function urlForRegist($url_back = null, $ext = null)
    {
        return UserAction::_()->urlForRegist($url_back, $ext);
    }
    public function urlForLogin($url_back = null, $ext = null)
    {
        return UserAction::_()->urlForLogout($url_back, $ext);
    }
    public function urlForLogout($url_back = null, $ext = null)
    {
        return UserAction::_()->urlForLogout($url_back, $ext);
    }
    public function urlForHome($url_back = null, $ext = null)
    {
        return UserAction::_()->urlForHome($url_back, $ext);
    }
}
