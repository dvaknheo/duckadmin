<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Controller;
use DuckAdmin\Controller\Base as C;

use DuckAdmin\Business\AdminBusiness;
use DuckAdmin\Business\SessionBusiness;
use DuckAdmin\Business\BusinessException;

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
        //\DuckAdmin\Service\MySqlDumper::G()->foo();
    
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
        $flag = C::CheckCaptcha($post['captcha']);
        BusinessException::ThrowOn(!$flag,"验证码错误");
        $admin = AdminBusiness::G()->login($post);
        SessionBusiness::G()->setCurrentAdmin($admin,$post['remember']);
        C::ExitRouteTo('profile/index');
    }
    public function logout()
    {
        SessionBusiness::G()->logout();
        C::ExitRouteTo('');
    }
    public function captcha()
    {
        C::ShowCaptcha();
    }
}
