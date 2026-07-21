<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckUser\Controller;

use DuckPhp\GlobalUser\UserActionInterface;

use DuckUser\Business\UserBusiness;

class GlobalUserAction extends UserAction implements UserActionInterface
{
    public function service()
    {
        return UserBusiness::_Z();
    }
    public function getDataForView()
    {
        //
    }
    public function urlForLogin($url_back = null, $ext = null):string
    {
        return __url($url_back? "login?b=".__url($url_back):"login");
    }
    public function urlForLogout($url_back = null, $ext = null):string
    {
        return __url('logout');
    }
    public function urlForHome($url_back = null, $ext = null):string
    {
        return __url('Home/index');
    }
    public function urlForRegist($url_back = null, $ext = null):string
    {
        return __url('register');
    }
    public function batchGetUsernames($ids)
    {
        return UserBusiness::_()->batchGetUsernames($ids);
    }
}