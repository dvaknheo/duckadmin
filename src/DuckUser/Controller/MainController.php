<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckUser\Controller;

use DuckPhp\Foundation\SimpleControllerTrait;

use DuckUser\Business\UserBusiness;
use DuckUser\Controller\UserAction;
use DuckUser\Controller\UserAction as Helper;


class MainController
{
    use SimpleControllerTrait;
    
    public function action_index()
    {
        $url_reg = __url('register');
        $url_login = __url('login');
        
        Helper::Show(get_defined_vars(), 'main');
    }
    public function action_register()
    {
        $post = Helper::POST();
        if (!$post) {
            $csrf_field = Helper::_()->csrfField();
            $url_register = __url('register');
            
            Helper::Show(get_defined_vars(), 'register');
            return;
        }
        try {
            $user = UserAction::_()->register($post);
            Helper::GoHome();
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
            $csrf_field = UserAction::_()->csrfField();
            $url_login = __url('login');
            
            Helper::Show(get_defined_vars(),'login');
            return;
        }
        try {
            UserAction::_()->login($post);
            Helper::GoHome();
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
        
        Helper::ExitRouteTo('index');
    }
    ////////////////////////////////////////////
}
