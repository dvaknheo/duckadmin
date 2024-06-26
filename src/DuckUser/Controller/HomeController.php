<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckUser\Controller;

use DuckUser\Business\UserBusiness;
use DuckPhp\GlobalUser\UserControllerInterface;
class HomeController extends Base implements UserControllerInterface
{
    public function __construct()
    {
        parent::__construct();
    }
    public function action_index()
    {
        $url_logout = Helper::User()->urlForLogout();
        Helper::Show(get_defined_vars());
    }
    public function action_password()
    {
        $error = '';
        if (Helper::POST()) {
            try {
                $uid = Helper::UserId();
                $old_pass = Helper::POST('oldpassword','');
                $new_pass = Helper::POST('newpassword','');
                $confirm_pass = Helper::POST('newpassword_confirm','');
                
                Helper::ControllerThrowOn($new_pass !== $confirm_pass, '重复密码不一致');
                UserBusiness::_()->changePassword($uid, $old_pass, $new_pass);
                
                $error = "密码修改完毕"; 
            } catch (\Exception $ex) {
                $error = $ex->getMessage();
            }
        }
        Helper::Show(get_defined_vars());
    }
}