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
    public function login()
    {
        C::Show(get_defined_vars(), 'login');
    }
    public function do_login()
    {
        C::assignExceptionHandler(\Exception::class,function($ex){
            $error = $ex->getMessage();
            C::assignViewData(['error'=>$error]);
            C::Show(get_defined_vars(), 'login');
        });
        $post = C::POST();
        $admin = AdminService::G()->login($post);
        SessionService::G()->setCurrentAdmin($admin,$post['remember']);
        C::ExitRouteTo('profile/index');
    }
    public function logout()
    {
        SessionService::G()->logout();
        C::RedirectRouteTo('index');
    }
    public function xx()
    {
        captcha();
    }
    public function verify()
    {
        captcha_check();
        //CaptchaService::G()->show();
    }
}
