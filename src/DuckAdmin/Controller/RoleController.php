<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Controller;

use DuckAdmin\Controller\AdminAction as C;

/**
 * 系统设置
 */
class RoleController extends Base
{
    /**
     * 不需要鉴权的方法
     * @var array
     */
    protected $noNeedAuth = ['select'];

    /**
     * 浏览
     * @return Response
     */
    public function index()
    {
        C::Show([],'role/index');
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
		$id = $request->get('id');
		RoleBusiness::G()->selectRoles();
    }

    /**
     * 插入
     * @param 
     * @return Response
     * @throws BusinessException
     */
    public function insert()
    {
		if (!C::POST()) {
			C::Show([],'role/insert');
			return;
		}
        C::ThrowOn(true,"No Impelement");
		$post = C::POST();
		$id = RoleBusiness::G()->insertRole($post);
		C::Success(['id' => $id]);

    }

    /**
     * 更新
     * @param 
     * @return Response
     * @throws BusinessException
     */
    public function update()
    {
		if (!C::POST()) {
			C::Show([],'role/update');
			return;
		}
		C::ThrowOn(true,"No Impelement");
		$post = C::POST();
		$id = RoleBusiness::G()->updateRole($post);
		C::Success(['id' => $id]);
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
		$post = C::POST();
		$id = RoleBusiness::G()->deleteRole($post);
        return $this->json(0);
    }

    /**
     * 获取角色权限
     * @param 
     * @return Response
     */
    public function rules()
    {
		C::ThrowOn(true,"No Impelement");
        $role_id = C::GET('id');
		$tree = RoleBusiness::G()->tree($role_id);
        C::Success($tree);
    }
}
