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
        var_dump("???");
    }

    /**
     * 获取配置
     * @return Response
     */
    public function get()
    {
		$s=file_get_contents(__DIR__.'/data/config.json');
		C::ExitJson(json_decode($s,true));
    }

    /**
     * 更改
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function update()
    {
    }
}
