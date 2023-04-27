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
class RoleController extends ProjectController
{
    /**
     * 不需要鉴权的方法
     * @var array
     */
    protected $noNeedAuth = ['select'];

    /**
     * @var Role
     */
    protected $model = null;
	
    /**
     * 浏览
     * @return Response
     */
    public function index()
    {
		C::ThrowOn(true,"No Impelement");
        //return view('role/index');
    }

    /**
     * 查询
     * @param 
     * @return Response
     * @throws BusinessException
     */
    public function select()
    {
		C::ThrowOn(true,"No Impelement");
    }

    /**
     * 插入
     * @param 
     * @return Response
     * @throws BusinessException
     */
    public function insert()
    {
		C::ThrowOn(true,"No Impelement");
    }

    /**
     * 更新
     * @param 
     * @return Response
     * @throws BusinessException
     */
    public function update()
    {
		C::ThrowOn(true,"No Impelement");
    }

    /**
     * 删除
     * @param 
     * @return Response
     * @throws BusinessException
     */
    public function delete()
    {
		C::ThrowOn(true,"No Impelement");
    }

    /**
     * 获取角色权限
     * @param 
     * @return Response
     */
    public function rules()
    {
		C::ThrowOn(true,"No Impelement");
    }
}
