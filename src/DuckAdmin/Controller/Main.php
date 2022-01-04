<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Controller;

use DuckAdmin\Business\AdminBusiness;
use DuckAdmin\Controller\Base as C;
use DuckAdmin\ControllerEx\AdminSession;
use DuckAdmin\ControllerEx\CaptchaAction;
/**
 * 主入口
 */
class Main extends Base
{
    /**
     * 首页
     */
    public function index()
    {
        C::Show(get_defined_vars(), 'index');
    }
    public function do_index()
    {
        $this->doLogin();
    }
    /**
     * 登录处理
     */
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
    /**
     * 登出
     */
    public function logout()
    {
        AdminSession::G()->logout();
        C::ExitRouteTo('');
    }
    /**
     * 验证码
     */
    public function captcha()
    {
        CaptchaAction::ShowCaptcha();
    }
}
