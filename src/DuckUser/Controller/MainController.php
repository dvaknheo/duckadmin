<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckUser\Controller;

use DuckPhp\Core\App;
use DuckUser\Business\UserBusiness;

class MainController extends Base
{
    public function __construct()
    {
        // this override for skip auth
    }
    public function action_index()
    {
        $url_reg = UserAction::_()->urlForRegist();
        $url_login = UserAction::_()->urlForLogin();
        
        Helper::Show(get_defined_vars(), 'main');
    }
    public function action_register()
    {
        $post = Helper::POST();
        
        if (!$post) {
            $csrf_field = Helper::_()->csrfField();
            $url_register = UserAction::_()->urlForRegist();
            
            Helper::Show(get_defined_vars(), 'register');
            return;
        }
        try {
            $user = UserAction::_()->regist($post);
            Helper::_()->goHome();
        } catch (\Exception $ex) {
            $error = $ex->getMessage();
            $name = Helper::POST('name', '');
            Helper::Show(get_defined_vars(), 'register');
            return;
        }

    }
    public function action_login()
    {
        $post = Helper::POST();
        if (!$post) {
            $csrf_field = Helper::_()->csrfField();
            $url_login = UserAction::_()->urlForLogin();
            $back_url = Helper::GET('b','');
            Helper::Show(get_defined_vars(),'login');
            return;
        }
        try {
            UserAction::_()->login($post);
            $back_url = Helper::GET('b','');
            
            if(!$back_url){
                Helper::_()->goHome();
            }else{
                $last_phase = App::Phase(App::Root());
                $back_url = __url($back_url);
                Helper::Show302($back_url);
                App::Phase($last_phase);
            }
        } catch (\Exception $ex) {
            $error = $ex->getMessage();
            $name =  __h( Helper::POST('name', ''));
            Helper::Show(get_defined_vars(), 'login');
            return;
        }
    }
    public function action_logout()
    {
        UserAction::_()->logout();
        
        Helper::Show302('index');
    }
}
