<?php
namespace DuckUserManager\Controller;

use DuckPhp\Core\SystemWrapper;
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
        $all = Helper::GET('all',false);
        [$total, $list]= UserBusiness::_()->getUserList(['all'=>$all],Helper::PageNo());
        
        $list2 =[];
        foreach($list as $v){
            $t['id'] = $v['id'];
            $t['username'] = __h($v['username']);
            $hash = $this->getHash($t['id']);
            $t['url_delete'] = __url('delete?id='.$v['id'].'&hash='.$hash);
            $list2[]=$t;
        }
        
        $data['list'] =$list2;
        $data['pager'] = Helper::PageHtml($total);
        
        Helper::Show($data,'user/index');
    }

    /**
     * 删除
     * @param 
     * @return Response
     */
    public function action_delete()
    {
        $hash = Helper::Get('hash');
        $id = Helper::Get('id');
        
        $this->checkHash($id);
        
        $ret = UserBusiness::_()->changeUserStatus(Helper::AdminId(), $id);
		Helper::ShowJson($ret);
    }
    protected function getHash($id)
    {
        $session_id = SystemWrapper::session_id();
        return md5($session_id.$v['id']);
    }
    protected function checkHash($id,$hash)
    {
        $session_id = SystemWrapper::session_id();
        $new_hash = md5($session_id.$id);
        Helper::ControllerThrowOn($new_hash!==$hash,'校检失败');
    }

}