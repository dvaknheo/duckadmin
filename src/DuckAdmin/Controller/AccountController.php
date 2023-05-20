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
		C::Show([],'account/index');
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
		
		try{
			$flag = CaptchaAction::G()->doCheckCaptcha($captcha);
			C::ThrowOn(!$flag, '验证码错误',1);
			
			$admin = AccountBusiness::G()->login($username, $password);
			AdminSession::G()->setCurrentAdmin($admin);
			
			C::Success($admin,'登录成功');
		}catch(\Throwable $ex){
			C::json($ex->getCode(), $ex->getMessage(), []);
		}
	}

    /**
     * 退出
     * @param 
     * @return Response
     */
    public function logout()
    {
		AdminSession::G()->setCurrentAdmin([]);
        C::Success(0);
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
		$data['token'] = 'TODO TOKEN';////AdminSession::G()->SessionId();
		
		C::Success($data);
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
        $password = $request->post('password');
		$request->post('password_confirm');
		$request->post('old_password');
		
        $hash = Admin::find(admin_id())['password'];
        if (!$password) {
            return $this->json(2, '密码不能为空');
        }
        if ($request->post('password_confirm') !== $password) {
            return $this->json(3, '两次密码输入不一致');
        }
        if (!Util::passwordVerify($request->post('old_password'), $hash)) {
            return $this->json(1, '原始密码不正确');
        }
        $update_data = [
            'password' => Util::passwordHash($password)
        ];
        Admin::where('id', admin_id())->update($update_data);
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
