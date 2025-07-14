<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Controller;
use DuckPhp\Core\App;

use DuckAdmin\Business\AccountBusiness;
use DuckAdmin\Business\InstallBusiness;

/**
 * 主入口
 */
class MainController
{
    public function __construct()
    {
    }
    /**
     * 首页
     */
    public function index()
    {
        $admin_id = AdminAction::_()->getAdminIdBySession();
        if (!$admin_id) {
            $url_back = Helper::GET('back_url','');
            $url_back = $url_back === '/' ? '': $url_back;
            $url_back = $url_back?$url_back:__url('account/dashboard');
            if($url_back){
                $last_phase = App::Phase(App::Root()->getOverridingClass());
                $url_back = __url(ltrim($url_back,'/'));
                App::Phase($last_phase);
            }
            Helper::Show(['url_back'=>$url_back], 'account/login');
            return;
        }
        Helper::Show302('account/dashboard');
    }
    public function login()
    {
        if(!Helper::Post()){
            Helper::Show302(__url('index'));
        }
        $username = Helper::Post('username', '');
        $password = Helper::Post('password', '');
        $captcha = Helper::Post('captcha');
        
        //这里有更好判断方法不需要特殊化，TODO 用上
        $flag = AdminAction::_()->doCheckCaptcha($captcha);
        Helper::ControllerThrowOn(!$flag, '验证码错误',1);

        
        $admin = AccountBusiness::_()->login($username, $password);
        AdminAction::_()->setCurrentAdmin($admin);
        return Helper::Success($admin);
    }
    /**
     * 退出
     * @param 
     * @return Response
     */
    public function logout()
    {
        AdminAction::_()->logout();
        if(Helper::IsAjax()){
            Helper::Success(0);
        }else{
            Helper::Show302(__url('index'));
        }
    }
    /**
     * 验证码
     * @param 
     * @param string $type
     * @return Response
     */
    public function captcha()
    {
        AdminAction::_()->doShowCaptcha();
    }
}
