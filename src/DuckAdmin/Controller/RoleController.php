<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Controller;

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
        return Helper::Show([],'role/index');
    }

    /**
     * 查询
     * @param 
     * @return Response
     * @throws BusinessException
     */
    public function select()
    {
		$post = Helper::GET();
		$id = Helper::GET('id');
		[$data,$total] = RoleBusiness::_()->selectRoles(Helper::AdminId(), $id, $post);
		return Helper::Success($data,$total);
    }

    /**
     * 插入
     * @param 
     * @return Response
     * @throws BusinessException
     */
    public function insert()
    {
		if (!Helper::POST()) {
			return Helper::Show([],'role/insert');
		}
		$post = Helper::POST();
		
		$id = RoleBusiness::_()->insertRole(Helper::AdminId(), $post);
		return Helper::Success(['id' => $id]);

    }

    /**
     * 更新
     * @param 
     * @return Response
     * @throws BusinessException
     */
    public function update()
    {
		if (!Helper::POST()) {
			return Helper::Show([],'role/update');
		}
		$post = Helper::POST();
		
		$id = RoleBusiness::_()->updateRole(Helper::AdminId(), $post);
		return Helper::Success(['id' => $id]);
    }

    /**
     * 删除
     * @param 
     * @return Response
     * @throws BusinessException
     */
    public function delete()
    {
		$post = Helper::POST();
		$id = RoleBusiness::_()->deleteRole(Helper::AdminId(), $post['id']);
        return  Helper::Success();
    }

    /**
     * 获取角色权限
     * @param 
     * @return Response
     */
    public function rules()
    {
		$role_id = Helper::GET('id');
		
		$tree = RoleBusiness::_()->tree(Helper::AdminId(), $role_id);
        return Helper::Success($tree);
    }
}
