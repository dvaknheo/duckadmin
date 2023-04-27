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
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function login(Request $request): Response
    {
    }

    /**
     * 退出
     * @param Request $request
     * @return Response
     */
    public function logout(Request $request): Response
    {
    }

    /**
     * 获取登录信息
     * @param Request $request
     * @return Response
     */
    public function info()
    {
		
		$data = AccountBusiness::G()->getAccountInfo();
		C::ExitJson($data);
    }

    /**
     * 更新
     * @param Request $request
     * @return Response
     */
    public function update(Request $request)
    {
        
    }

    /**
     * 修改密码
     * @param Request $request
     * @return Response
     */
    public function password()
    {

    }

    /**
     * 验证码
     * @param Request $request
     * @param string $type
     * @return Response
     */
    public function captcha()
    {
	
    }

}
