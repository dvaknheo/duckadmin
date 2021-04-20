<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Controller;
use DuckAdmin\Helper\ControllerHelper as C;

class Main extends BaseController
{
    public function __construct()
    {
        //parent::__construct();
    }
    
    public function index()
    {
        C::Show(get_defined_vars(), 'index');
    }
    public function main()
    {
        C::Show(get_defined_vars(), 'main');
    }
    
    public function login()
    {
        $error = '';
        $post = C::POST();
        if($post){
            try{
                $user = AdminService::G()->login($post);
                SessionService::G()->setCurrentUser($user);
                C::RedirectRouteTo('profile/index');
                return;
            }catch(\Throwable $ex){
                $error = $ex->getMessage();
            }
        }
    
        C::Show(get_defined_vars(), 'login');
    }
    public function logout()
    {
        SessionService::G()->logout();
        C::RedirectRouteTo('index');
    }
    public function verify()
    {
        CaptchaService::G()->show();
    }
}
