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
    
    public function index()
    {
        $url_reg = __url('register');
        $url_login = __url('login');
        
        Helper::Show(get_defined_vars(), 'main');
    }
    public function register()
    {
        $csrf_field = Helper::_()->csrfField();
        $url_register = __url('register');
        
        Helper::Show(get_defined_vars(), 'register');
    }
    public function login()
    {
        $csrf_field = Helper::_()->csrfField();
        $url_login = __url('login');
        
        Helper::Show(get_defined_vars(),'login');
    }
    public function logout()
    {
        UserAction::G()->logout();
        
        Helper::ExitRouteTo('index');
    }
    ////////////////////////////////////////////
    public function do_register()
    {
        $post = Helper::POST();
        try {
            $user = UserAction::G()->register($post);
            Helper::GoHome();
        } catch (\Exception $ex) {
            $error = $ex->getMessage();
            $name = Helper::POST('name', '');
            Helper::Show(get_defined_vars(), 'register');
            return;
        }
    }
    public function do_login()
    {
        $post = Helper::POST();
        try {
            UserAction::G()->login($post);
            Helper::GoHome();
        } catch (\Exception $ex) {
            $error = $ex->getMessage();
            $name =  __h( Helper::POST('name', ''));
            Helper::Show(get_defined_vars(), 'login');
            return;
        }
    }
}
