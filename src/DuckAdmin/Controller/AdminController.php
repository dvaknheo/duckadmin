<?php
namespace DuckAdmin\Controller;

use DuckAdmin\Business\AdminBusiness;

/**
 * 管理员列表 
 */
class AdminController extends Base
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
        return Helper::Show([],'admin/index');
    }

    /**
     * 查询
     * @param 
     * @return Response
     * @throws BusinessException
     */
    public function select()
    {
		$input = Helper::GET();
		[$data, $count] = AdminBusiness::_()->showAdmins(Helper::AdminId(),$input);
        return Helper::Success($data,$count);
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
			return Helper::Show([],'admin/insert');
		}
		$input = Helper::POST();
		$admin_id = AdminBusiness::_()->addAdmin(Helper::AdminId(), $input);
		return Helper::Success(['id' => $admin_id]);
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
			return Helper::Show([],'admin/update');
		}
		$post = Helper::POST();
		AdminBusiness::_()->updateAdmin(Helper::AdminId(), $post);
    }

    /**
     * 删除
     * @param 
     * @return Response
     */
    public function delete()
    {
		$post = Helper::POST();
		AdminBusiness::_()->deleteAdmin(Helper::AdminId(), $post['id']);
		return Helper::Success();
    }

}
