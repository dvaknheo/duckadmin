<?php
namespace DuckAdmin\User\Controller;

use DuckPhp\Foundation\Controller\AdminControllerBase;
use DuckAdmin\User\Business\UserAdminBusiness;

/**
 * 用户管理 
 */
class AdminController extends AdminControllerBase
{
    /**
     * 首页
     */
    public function index()
    {
        $all = Helper::GET('all',false);
        [$total, $list]= UserAdminBusiness::_()->getUserList(['all'=>$all], Helper::PageNo());
        
        $users =[];
        foreach($list as $v){
            $hash = Session::_()->getHash($v['id']);
            $t =[];
            $t['id'] = $v['id'];
            $t['username'] = __h($v['username']);
            
            $t['is_deleted'] = $v['deleted_at']?true:false;
            $t['url_delete'] = __url('user/delete?id='.$v['id'].'&hash='.$hash);
            $t['url_undelete'] = __url('user/undelete?id='.$v['id'].'&hash='.$hash);
            $users[]=$t;
        }
        
        $data['users'] =$users;
        $data['pager'] = Helper::PageHtml((int)$total);
        $data['is_all'] =$all?true:false;
        Helper::Show($data,'Admin/index');
    }

    /**
     * 删除
     */
    public function delete()
    {
        $hash = Helper::Get('hash');
        $id = Helper::Get('id');
        
        Session::_()->checkHash($id,$hash);
        
        $ret = UserAdminBusiness::_()->deleteUser(Helper::AdminId(), $id);
		Helper::Show302('user/index');
    }
    /**
     * 还原
     */
    public function undelete()
    {
        $hash = Helper::Get('hash');
        $id = Helper::Get('id');
        
        Session::_()->checkHash($id,$hash);
        
        $ret = UserAdminBusiness::_()->unDeleteUser(Helper::AdminId(), $id);
		Helper::Show302('user/index');
    }
}