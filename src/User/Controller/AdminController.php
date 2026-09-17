<?php
namespace DuckAdmin\User\Controller;

use DuckPhp\Foundation\Controller\AdminControllerBase;
use DuckAdmin\User\Business\UserAdminBusiness;

/**
 * 管理员列表 
 */
class AdminController extends AdminControllerBase
{
    /**
     * 首页
     */
    public function index()
    {
        $all = Helper::GET('all',false);
        [$total, $list]= UserAdminBusiness::_()->getUserList(['all'=>$all],Helper::PageNo());
        
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
        
        $this->checkHash($id,$hash);
        
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
        
        $this->checkHash($id,$hash);
        
        $ret = UserAdminBusiness::_()->unDeleteUser(Helper::AdminId(), $id);
		Helper::Show302('user/index');
    }
    
    protected function getHash($id)
    {
        return '';
        $hash_id = Helper::SESSION('hash',null);
        $hash_id = $hash_id??mt_rand(1,999999);
        $_SESSION['hash']=$hash_id;
        return md5($hash_id.'|'.$id);
    }
    protected function checkHash($id,$hash)
    {
        return ;
        @session_start();
        $hash_id = Helper::SESSION('hash',null);
        Helper::ControllerThrowOn($hash_id===null,"校[$id, $hash, $new_hash]检失败!");
        $new_hash = md5($hash_id.'|'.$id);
        Helper::ControllerThrowOn($new_hash!==$hash,"校 [$id, $hash, $new_hash] 检失败");
    }

}