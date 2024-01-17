<?php
namespace DuckUserManager\Controller;

use DuckPhp\Foundation\SimpleControllerTrait;
use DuckUserManager\Business\UserBusiness;
use DuckUserManager\System\DuckUserManagerApp as Helper;

/**
 * 管理员列表 
 */
class UserController
{
    use SimpleControllerTrait;

    public function __construct()
    {
        Helper::Admin()->checkLogin();
    }
    /**
     * 浏览
     * @return Response
     */
    public function action_index()
    {
        Helper::Show([],'user/index');
    }

    /**
     * 删除
     * @param 
     * @return Response
     */
    public function action_disable()
    {
		//$post = Helper::POST();
		$ret = UserBusiness::_()->changeUserStatus(Helper::UserId(), $post['id']);
		Helper::ShowJson($ret);
    }

}