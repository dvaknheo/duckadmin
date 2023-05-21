<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Controller;

use DuckAdmin\Business\AccountBusiness;
use DuckAdmin\Controller\AdminAction as C;

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
		return C::Show([],'account/index');
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
		
		$admin = AccountBusiness::G()->login($username, $password);
		AdminSession::G()->setCurrentAdmin($admin);
		
		return C::Success($admin);  // 这里有问题 //'登录成功' 是message 里的，这是个非标准的
	}

    /**
     * 退出
     * @param 
     * @return Response
     */
    public function logout()
    {
		AdminSession::G()->setCurrentAdmin([]);
        return C::Success(0);
    }

    /**
     * 获取登录信息
     * @param 
     * @return Response
     */
    public function info()
    {
		$admin = AdminSession::G()->getCurrentAdmin();
		$data = AccountBusiness::G()->getAccountInfo($admin);
		//$data['token'] = 'TODO TOKEN';////AdminSession::G()->SessionId();
		
		return C::Success($data);
    }

    /**
     * 更新
     * @param 
     * @return Response
     */
    public function update()
    {
        $data = C::POST();
		
		$admin = AccountBusiness::G()->update($data);
        AdminSession::G()->setCurrentAdmin($admin);
		
        C::Sucess();
    }

    /**
     * 修改密码
     * @param 
     * @return Response
     */
    public function password()
    {
        $password = C::POST('password');
		$password_confirm = C::POST('password_confirm');
		$old_password = C::POST('old_password');
		
		AccountBusiness::G()->changePassword($old_password, $password, $password_confirm );
		C::Sucess();
    }
    /**
     * 验证码
     * @param 
     * @param string $type
     * @return Response
     */
    public function captcha()
    {
		CaptchaAction::G()->doShowCaptcha();
    }
}
