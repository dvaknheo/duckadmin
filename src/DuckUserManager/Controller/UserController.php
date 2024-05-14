<?php
namespace DuckUserManager\Controller;

use DuckPhp\Foundation\Controller\Helper;
use DuckPhp\Foundation\SimpleControllerTrait;
use DuckUserManager\Business\UserBusiness;

/**
 * 管理员列表 
 */
class UserController
{
    use SimpleControllerTrait;

    public function __construct()
    {
        $controller = Helper::getRouteCallingClass();
        $action = Helper::getRouteCallingMethod();
        
		$flag = Helper::Admin()->canAccessCall($controller,$action);
        
        Helper::ControllerThrowOn(!$flag,"请登录");
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