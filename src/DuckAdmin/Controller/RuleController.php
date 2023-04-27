<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Controller;

use DuckAdmin\System\ProjectController;
use DuckAdmin\System\ControllerHelper as C;

/**
 * 权限菜单
 */
class RuleController extends ProjectController
{
    /**
     * 不需要权限的方法
     *
     * @var string[]
     */
    protected $noNeedAuth = ['get', 'permission'];


    /**
     * 浏览
     * @return Response
     */
    public function index()
    {
        return view('rule/index');
    }

    /**
     * 查询
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function select()
    {
        C::ThrowOn(true,"No Impelement");
    }

    /**
     * 获取菜单
     * @param Request $request
     * @return Response
     */
    function get()
    {
		$s=file_get_contents(__DIR__.'/data/rule.json');
		C::ExitJson(json_decode($s,true));
    }

    /**
     * 获取权限
     * @param Request $request
     * @return Response
     */
    public function permission()
    {
        C::ThrowOn(true,"No Impelement");
    }


    /**
     * 添加
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function insert()
    {
		C::ThrowOn(true,"No Impelement");
    }

    /**
     * 更新
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function update()
    {
		C::ThrowOn(true,"No Impelement");
    }
    
    /**
     * 删除
     * @param Request $request
     * @return Response
     */
    public function delete()
    {
		C::ThrowOn(true,"No Impelement");
    }
}
