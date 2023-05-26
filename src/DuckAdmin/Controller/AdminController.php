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
     * 开启auth数据限制
     * @var string
     */
    //protected $dataLimit = 'auth';

    /**
     * 以id为数据限制字段
     * @var string
     */
    //protected $dataLimitField = 'id';

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
		$dataLimitField = 'id';
		$dataLimit = 'auth';
		$admin_id = AdminAction::G()->getCurrentAdmin()['id'];
		[$data, $count] = AdminBusiness::G()->showAdmins($admin_id,$input,$dataLimit,$dataLimitField);
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
		$op_id = AdminAction::G()->getCurrentAdmin()['id'];
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
		AdminBusiness::G()->updateAdmin($post);
    }

    /**
     * 删除
     * @param 
     * @return Response
     */
    public function delete()
    {
		$post = C::POST();
		$op_id = AdminAction::G()->getCurrentAdmin()['id'];
		AdminBusiness::G()->deleteAdmin($op_id, $post['id']);
		return C::Success();
    }

}
