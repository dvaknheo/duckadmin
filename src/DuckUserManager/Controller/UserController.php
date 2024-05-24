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
        Helper::Admin()->checkAccess();
    }
    /**
     * 浏览
     * @return Response
     */
    public function action_index()
    {
        $list = UserBusiness::_()->getUserList([],Helper::PageNo());
        $data['list'] =$list['data'];
        $data['pager'] = Helper::Pager($list['count']);
        Helper::Show($data,'user/index');
    }

    /**
     * 删除
     * @param 
     * @return Response
     */
    public function action_disable()
    {
        $ret = UserBusiness::_()->changeUserStatus(Helper::AdminId(), Helper::POST('id'));
		Helper::ShowJson($ret);
    }

}