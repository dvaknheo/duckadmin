<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Controller;
use DuckAdmin\App\ControllerHelper as C;
use DuckAdmin\Service\AdminService;
use DuckAdmin\Service\SessionService;

class Main extends BaseController
{
    public function __construct()
    {
        // 我们只需要 BaseController 的方法，不需要初始化检查
        $this->initialize();
    }
    protected function initialize()
    {
        //for override
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
                $admin = AdminService::G()->login($post);
                SessionService::G()->setCurrentAdmin($admin,$post['remember']);
                C::ExitRouteTo('profile/index');
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
        //CaptchaService::G()->show();
    }
}
