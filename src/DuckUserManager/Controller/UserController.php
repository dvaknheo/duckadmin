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
        $controller =  Helper::getRouteCallingClass();
        $action =  Helper::getRouteCallingMethod();
        $url = __url(Helper::PathInfo());
        Helper::Admin()->checkAccess($controller, $action, $url);
    }
    /**
     * 浏览
     * @return Response
     */
    public function action_index()
    {
        $list = UserBusiness::_()->getUserList([],Helper::PageNo());
        $data['list'] =$list['data'];
        $data['pager'] = Helper::PageHtml($list['count']);
        Helper::Show($data,'user/index');
    }

    /**
     * 删除
     * @param 
     * @return Response
     */
    public function action_status()
    {
        $ret = UserBusiness::_()->changeUserStatus(Helper::AdminId(), Helper::POST('id'));
		Helper::ShowJson($ret);
    }

}