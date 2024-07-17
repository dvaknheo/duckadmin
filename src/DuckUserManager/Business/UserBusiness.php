<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckUserManager\Business;

use DuckPhp\Foundation\SimpleBusinessTrait;
use DuckUserManager\Model\UserModel;

class UserBusiness
{
    use SimpleBusinessTrait;
    
    public function getUserList($conditions=[],$page = 1, $page_size = 10)
    {
        //我们这里要加个显示被禁用的用户等
        return UserModel::_()->getUserList($conditions, $page, $page_size);
    }
    public function deleteUser($admin_id,$id)
    {
        $ret = UserModel::G()->disable($id);
        //ActionLogModel::_()->log("禁用 {$id}，结果", "调整用户");
    }
    public function unDeleteUser($admin_id,$id)
    {
        //$ret = UserModel::G()->disable($id);
    }
}