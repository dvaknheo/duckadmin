<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Controller;

use DuckAdmin\System\ProjectController;
use DuckAdmin\System\ControllerHelper as C;

/**
 * 系统设置
 */
class account extends ProjectController
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
		$s=file_get_contents(__DIR__.'/account.json');
		C::ExitJson(json_decode($s,true));
    }

    /**
     * 更新
     * @param Request $request
     * @return Response
     */
    public function update(Request $request): Response
    {
        
    }

    /**
     * 修改密码
     * @param Request $request
     * @return Response
     */
    public function password(Request $request): Response
    {

    }

    /**
     * 验证码
     * @param Request $request
     * @param string $type
     * @return Response
     */
    public function captcha(Request $request, string $type = 'login'): Response
    {
    }

}
