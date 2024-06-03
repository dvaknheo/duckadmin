<?php
namespace DuckUserManager\Controller;

use DuckPhp\Foundation\Controller\Helper;
use DuckPhp\Foundation\SimpleControllerTrait;
use DuckPhp\GlobalAdmin\AdminControllerInterface;
use DuckUserManager\Business\UserBusiness;

/**
 * 管理员列表 
 */
class UserController implements AdminControllerInterface
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
        [$total, $list]= UserBusiness::_()->getUserList([],Helper::PageNo());
        $data['list'] =$list;
        $data['pager'] = Helper::PageHtml($total);
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