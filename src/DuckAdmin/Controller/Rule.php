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
class rule extends ProjectController
{
    /**
     * 不需要权限的方法
     *
     * @var string[]
     */
    protected $noNeedAuth = ['get', 'permission'];

    /**
     * @var Rule
     */
    protected $model = null;

    /**
     * 构造函数
     */
    public function __construct()
    {
    }

    /**
     * 浏览
     * @return Response
     */
    public function index(): Response
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
        return parent::select($request);
    }

    /**
     * 获取菜单
     * @param Request $request
     * @return Response
     */
    function get()
    {
		$s=file_get_contents(__DIR__.'/rule.json');
		C::ExitJson(json_decode($s,true));
    }

    /**
     * 获取权限
     * @param Request $request
     * @return Response
     */
    public function permission()
    {
        $rules = $this->getRules(admin('roles'));
        // 超级管理员
        if (in_array('*', $rules)) {
            return $this->json(0, 'ok', ['*']);
        }
        return $this->json(0, 'ok', $permissions);
    }

    /**
     * 查询前置方法
     * @param Request $request
     * @return array
     * @throws BusinessException
     */
    protected function selectInput(Request $request): array
    {
	static::ThrowOn(true,"No Impelement");
	}

    /**
     * 添加
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function insert()
    {
		static::ThrowOn(true,"No Impelement");
    }

    /**
     * 更新
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function update()
    {
		static::ThrowOn(true,"No Impelement");
    }
    
    /**
     * 删除
     * @param Request $request
     * @return Response
     */
    public function delete()
    {
		static::ThrowOn(true,"No Impelement");
    }
}
