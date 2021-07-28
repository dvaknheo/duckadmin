<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Controller;

use DuckAdmin\Business\AdminBusiness;
use DuckAdmin\Controller\Base as C;
use DuckAdmin\ControlllerEx\AdminSession;
use DuckAdmin\ControlllerEx\CaptchaAction;

class Main extends Base
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
    public function do_index()
    {
        $this->doLogin();
    }
    public function do_login()
    {
        $this->doLogin();
    }
    protected function doLogin()
    {
        C::assignExceptionHandler(\Exception::class,function($ex){
            $error = $ex->getMessage();
            C::assignViewData(['error'=>$error]);
            C::Show(get_defined_vars(),'index');
        });
        
        $post = C::POST();
        $flag = CaptchaAction::CheckCaptcha($post['captcha']);
        AdminBusiness::ThrowOn(!$flag,"验证码错误");
        $admin = AdminBusiness::G()->login($post);
        AdminSession::G()->setCurrentAdmin($admin,$post['remember']);
        C::ExitRouteTo('Profile/index');  // 这里要设置成可配置的
    }
    public function logout()
    {
        AdminSession::G()->logout();
        C::ExitRouteTo('');
    }
    public function captcha()
    {
        CaptchaAction::ShowCaptcha();
    }
}
