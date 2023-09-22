<?php
namespace DuckAdmin\Controller;
use DuckAdmin\Controller\AdminAction as C;
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
        return C::Show([],'admin/index');
    }

    /**
     * 查询
     * @param 
     * @return Response
     * @throws BusinessException
     */
    public function select()
    {
		$input = C::GET();
		$op_id = AdminAction::G()->getCurrentAdminId();
		[$data, $count] = AdminBusiness::G()->showAdmins($op_id,$input);
        return C::Success($data,$count);
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
			return C::Show([],'admin/insert');
		}
		$input = C::POST();
		$op_id = AdminAction::G()->getCurrentAdminId();
		$admin_id = AdminBusiness::G()->addAdmin($op_id, $input);
		return C::Success(['id' => $admin_id]);
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
			return C::Show([],'admin/update');
		}
		$post = C::POST();
		$op_id = AdminAction::G()->getCurrentAdminId();
		AdminBusiness::G()->updateAdmin($op_id, $post);
    }

    /**
     * 删除
     * @param 
     * @return Response
     */
    public function delete()
    {
		$post = C::POST();
		$op_id = AdminAction::G()->getCurrentAdminId();
		AdminBusiness::G()->deleteAdmin($op_id, $post['id']);
		return C::Success();
    }

}
