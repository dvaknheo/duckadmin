<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Controller;

use DuckAdmin\System\ControllerHelper as C;
use DuckAdmin\Business\AccountBusiness;

/**
 * 系统设置
 */
class AccountController extends Base
{
    /**
     * 不需要登录的方法
     * @var string[]
     */
    protected $noNeedLogin = ['login', 'logout', 'captcha'];

    /**
     * 不需要鉴权的方法
     * @var string[]
     */
    protected $noNeedAuth = ['info'];

    /**
     * 账户设置
     * @return Response
     */
    public function index()
    {
		C::Show('account/index');
    }

    /**
     * 登录
     * @param 
     * @return Response
     * @throws BusinessException
     */
    public function login()
    {
        $username = C::Post('username', '');
        $password = C::Post('password', '');
        $captcha = C::Post('captcha');
		
		$flag = CaptchaAction::G()->doCheckCaptcha($captcha);
        C::ThrowOn(!$flag, '验证码错误',1);
		
		$admin = AccountBusiness::G()->login($username,$password,$captcha);
		AdminSesseion::G()->setCurrentAdmin($admin);
		
		C::Sucess($admin,'登录成功');
	}

    /**
     * 退出
     * @param 
     * @return Response
     */
    public function logout()
    {
		AdminSesseion::G()->setCurrentAdmin([]);
        C::Sucess(0);
    }

    /**
     * 获取登录信息
     * @param 
     * @return Response
     */
    public function info()
    {
		$data = AccountBusiness::G()->getAccountInfo();
		$data['token'] = AdminSesseion::G()->SessionId();
		
		C::Sucess($data);
    }

    /**
     * 更新
     * @param 
     * @return Response
     */
    public function update()
    {
		C::ThrowOn(true,"No Impelement");
    }

    /**
     * 修改密码
     * @param 
     * @return Response
     */
    public function password()
    {
		C::ThrowOn(true,"No Impelement");

    }

    /**
     * 验证码
     * @param 
     * @param string $type
     * @return Response
     */
    public function captcha()
    {
		C::ThrowOn(true,"No Impelement");
    }
    /**
     * 解除登录频率限制
     * @param $username
     * @return void
     */

}
