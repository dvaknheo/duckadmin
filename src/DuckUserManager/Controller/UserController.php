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
        
        $users =[];
        foreach($list as $v){
            $hash = $this->getHash($v['id']);
            $t =[];
            $t['id'] = $v['id'];
            $t['username'] = __h($v['username']);
            
            $t['is_deleted'] = $v['deleted_at']?true:false;
            $t['url_delete'] = __url('user/delete?id='.$v['id'].'&hash='.$hash);
            $t['url_undelete'] = __url('user/undelete?id='.$v['id'].'&hash='.$hash);
            $users[]=$t;
        }
        
        $data['users'] =$users;
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
        
        $this->checkHash($id,$hash);
        
        $ret = UserBusiness::_()->deleteUser(Helper::AdminId(), $id);
		Helper::Show302('user/index');
    }
    /**
     * 还原
     * @param 
     * @return Response
     */
    public function action_undelete()
    {
        $hash = Helper::Get('hash');
        $id = Helper::Get('id');
        
        $this->checkHash($id,$hash);
        
        $ret = UserBusiness::_()->unDeleteUser(Helper::AdminId(), $id);
		Helper::Show302('user/index');
    }
    
    protected function getHash($id)
    {
        $session_id = SystemWrapper::session_id();
        return md5($session_id.$id);
    }
    protected function checkHash($id,$hash)
    {
        $session_id = SystemWrapper::session_id();
        $new_hash = md5($session_id.$id);
        Helper::ControllerThrowOn($new_hash!==$hash,'校检失败');
    }

}