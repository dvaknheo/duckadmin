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
     * 以id为数据限制字段
     * @var string
     */
    protected $dataLimitField = 'id'; //TODO 了解并删除

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
		$post = C::REQUEST();
		[$count, $data] = AdminBusiness::G()->showAdmin($post);
        C::ExitJson(['code' => 0, 'msg' => 'ok', 'count' => $count, 'data' => $data]);
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
        C::ThrowOn(true,"No Impelement");
		$post = C::POST();
		$admin = AdminBusiness::G()->addAdmin($post);
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
        C::ThrowOn(true,"No Impelement");

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
		AdminBusiness::G()->deleteAdmin($post);
    }

}
