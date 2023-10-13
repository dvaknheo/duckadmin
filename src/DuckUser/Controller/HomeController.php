<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckUser\Controller;

use DuckPhp\Foundation\SimpleControllerTrait;

use DuckUser\Business\UserBusiness;
use DuckUser\Controller\UserAction as Helper;

class HomeController
{
    use SimpleControllerTrait;
    
    public function action_index()
    {
        $url_logout = __url('logout');
        Helper::Show(get_defined_vars());
    }
    public function action_password()
    {
        Helper::Show(get_defined_vars());
    }
    //////////////////////////
    public function action_do_password()
    {
        $error = '';
        try {
            $uid = Helper::UserId();
            $old_pass = Helper::POST('oldpassword','');
            $new_pass = Helper::POST('newpassword','');
            $confirm_pass = Helper::POST('newpassword_confirm','');
            
            Helper::ThrowOn($new_pass !== $confirm_pass, '重复密码不一致');
            UserBusiness::G()->changePassword($uid, $old_pass, $new_pass);
            
            $error = "密码修改完毕"; 
        } catch (\Exception $ex) {
            $error = $ex->getMessage();
        }
        Helper::Show(get_defined_vars());
    }
}