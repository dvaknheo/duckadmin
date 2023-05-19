<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Controller;

use DuckAdmin\System\ProjectController;
use DuckAdmin\Controller\AdminAction as C;
use DuckAdmin\Business\ConfigBusiness;

/**
 * 系统设置
 */
class ConfigController extends ProjectController
{
    /**
     * 不需要验证权限的方法
     * @var string[]
     */
    protected $noNeedAuth = ['get'];

    /**
     * 账户设置
     * @return Response
     */
    public function index()
    {
		C::ThrowOn(true,"No Impelement");
    }

    /**
     * 获取配置
     * @return Response
     */
    public function get()
    {
		$data = ConfigBusiness::G()->getDefaultConfig();
		C::ExitJson($data); //注意这里不能用 success
    }

    /**
     * 更改
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function update()
    {
        C::ThrowOn(true,"No Impelement");
    }
}
