<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Controller;

use DuckAdmin\Controller\AdminAction as C;
use DuckAdmin\Business\RoleBusiness;

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
        return C::Show([],'role/index');
    }

    /**
     * 查询
     * @param 
     * @return Response
     * @throws BusinessException
     */
    public function select()
    {
		$post = C::GET();
		$id = C::GET('id');
		$admin_id = AdminAction::G()->getCurrentAdminId();
		$data = RoleBusiness::G()->selectRoles($admin_id,$id,$post);
		return C::Success($data);
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
			return C::Show([],'role/insert');
		}
        C::ThrowOn(true,"No Impelement");
		$post = C::POST();
		$id = RoleBusiness::G()->insertRole($post);
		return C::Success(['id' => $id]);

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
			return C::Show([],'role/update');
		}
		C::ThrowOn(true,"No Impelement");
		$post = C::POST();
		$id = RoleBusiness::G()->updateRole($post);
		return C::Success(['id' => $id]);
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
        return C::Success($tree);
    }
}
