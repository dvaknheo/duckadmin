<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Controller;

use DuckAdmin\System\ProjectController;
use DuckAdmin\System\ControllerHelper as C;
use DuckAdmin\Business\AccountBusiness;

/**
 * 系统设置
 */
class AccountController extends ProjectController
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
		
		/*
        $captcha = $request->post('captcha');
        if (strtolower($captcha) !== session('captcha-login')) {
            return $this->json(1, '验证码错误');
        }
		$request->session()->forget('captcha-login');
		*/
        

        C::ThrowOn(!$username, '用户名不能为空',1);
		
        //$this->checkLoginLimit($username);
        $admin = Admin::where('username', $username)->first();
        if (!$admin || !Util::passwordVerify($password, $admin->password)) {
            return $this->json(1, '账户不存在或密码错误');
        }
        if ($admin->status != 0) {
            return $this->json(1, '当前账户暂时无法登录');
        }
		//////////////////////////////////////////
        $admin->login_at = date('Y-m-d H:i:s');
        $admin->save();
        //$this->removeLoginLimit($username);
        $admin = $admin->toArray();
        $session = $request->session();
        unset($admin['password']);
        $session->set('admin', $admin);
		
		////////////
        return $this->json(0, '登录成功', [
            'nickname' => $admin['nickname'],
            'token' => $request->sessionId(),
        ]);    
		}

    /**
     * 退出
     * @param 
     * @return Response
     */
    public function logout()
    {
        $request->session()->delete('admin');
        return $this->json(0);
    }

    /**
     * 获取登录信息
     * @param 
     * @return Response
     */
    public function info()
    {
		C::GetCurrentAdmin();
		$data = AccountBusiness::G()->getAccountInfo();
		//$data['isSupperAdmin'] = C::isSupperAdmin(),
		//$data['token'] = C::SessionId();// $request->sessionId(),
		
		C::Sucess($data);
    }

    /**
     * 更新
     * @param 
     * @return Response
     */
    public function update()
    {
        
    }

    /**
     * 修改密码
     * @param 
     * @return Response
     */
    public function password()
    {

    }

    /**
     * 验证码
     * @param 
     * @param string $type
     * @return Response
     */
    public function captcha()
    {
	
    }
    /**
     * 解除登录频率限制
     * @param $username
     * @return void
     */
    protected function removeLoginLimit($username)
    {
        $limit_log_path = runtime_path() . '/login';
        $limit_file = $limit_log_path . '/' . md5($username) . '.limit';
        if (is_file($limit_file)) {
            unlink($limit_file);
        }
    }
}
