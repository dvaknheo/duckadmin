<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Controller;
use DuckAdmin\App\ControllerHelper as C;
use DuckAdmin\Service\AdminService;
use DuckAdmin\Service\SessionService;
use DuckAdmin\Service\SessionServiceException;
use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;

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
        SessionService::G();
        
        // C::CheckCapthca();
        $builder = new CaptchaBuilder();
        $flag = PhraseBuilder::comparePhrases($_SESSION['phrase']??'', $post['captcha']);
        
        SessionServiceException::ThrowOn(!$flag,"验证码错误");
         $admin = AdminService::G()->login($post);

        SessionService::G()->setCurrentAdmin($admin,$post['remember']);
        C::ExitRouteTo('profile/index');
    }
    public function logout()
    {
        SessionService::G()->logout();
        C::ExitRouteTo('');
        
    }
    public function captcha()
    {
        //C::ShowCaptcha();
        
        $phraseBuilder = new PhraseBuilder(4, '0123456789');
        $builder = new CaptchaBuilder(null, $phraseBuilder);
        SessionService::G();
        
        header('Content-type: image/jpeg');
        header('Cache-Control: no-cache, no-store, max-age=0, must-revalidate');
        $builder->build()->output();
        $_SESSION['phrase'] = $builder->getPhrase();
    }
    public function verify()
    {
        captcha_check();
        //CaptchaService::G()->show();
    }
}
