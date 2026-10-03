<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckAdmin\User\Business;

use DuckPhp\Foundation\Business\Base;
use DuckAdmin\User\Model\UserAdminModel;

class UserAdminBusiness extends Base
{
    public function getUserList($conditions=[],$page = 1, $page_size = 10)
    {
        //我们这里要加个显示被禁用的用户等
        return UserAdminModel::_()->getUserList($conditions, $page, $page_size);
    }
    public function deleteUser($admin_id,$id)
    {
        // 原来这里写成了 Heper::(拼写错误),一旦调用就是致命错误
        Helper::AdminService()->log($admin_id, "{$admin_id}禁用 {$id}，结果", "调整用户");
        $ret = UserAdminModel::_()->deleteUser($id);
        return $ret;
    }
    public function unDeleteUser($admin_id,$id)
    {
        $ret = UserAdminModel::_()->unDeleteUser($id);
        return $ret;
        //$ret = UserModel::G()->disable($id);
    }
}