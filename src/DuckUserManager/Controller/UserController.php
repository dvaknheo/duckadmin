<?php
namespace DuckUserManager\Controller;

use DuckUserManager\Business\UserBusiness;
use DuckAdmin\Controller\Helper;

/**
 * 管理员列表 
 */
class UserController
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
        return Helper::Show([],'user/index');
    }
    public function __construct()
    {
        Helper::Admin()->checkLogin();
    }

    /**
     * 查询
     * @param 
     * @return Response
     * @throws BusinessException
     */
    public function action_select()
    {
		$input = Helper::GET();
		[$data, $count] = UserBusiness::_()->showUsers($input);
        return Helper::Success($data,$count);
    }

    /**
     * 插入
     * @param 
     * @return Response
     * @throws BusinessException
     */
    public function action_insert()
    {
        if (!Helper::POST()) {
			return Helper::Show([],'user/insert');
		}
		$input = Helper::POST();
		$admin_id = UserBusiness::_()->addUser(Helper::UserId(), $input);
		return Helper::Success(['id' => $admin_id]);
    }

    /**
     * 更新
     * @param 
     * @return Response
     * @throws BusinessException
    */
    public function action_update()
    {
		if (!Helper::POST()) {
			return Helper::Show([],'user/update');
		}
		$post = Helper::POST();
		UserBusiness::_()->updateUser(Helper::UserId(), $post);
    }

    /**
     * 删除
     * @param 
     * @return Response
     */
    public function action_delete()
    {
		$post = Helper::POST();
		UserBusiness::_()->deleteUser(Helper::UserId(), $post['id']);
		return Helper::Success();
    }

}