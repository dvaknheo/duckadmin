<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckUserManager\Business;

use DuckPhp\Foundation\SimpleBusinessTrait;
use DuckPhp\Helper\BusinessHelperTrait;

use DuckUserManager\Model\UserModel;

/**
 * 我们偷懒，把 BusinessHelper 集成进这里,基类我们也不要了，毕竟只有一个
 * 绑定异常，
 */
class UserBusiness
{
    use SimpleBusinessTrait;
    use BusinessHelperTrait;
    
    public function __construct()
    {
    }
    public function getUserList($page = 1, $page_size = 10)
    {
        //我们这里要加个显示被禁用的用户等
        return UserModel::_()->getList($page, $page_size);
    }
    public function deleteUser($id)
    {
        $ret = UserModel::G()->delete($id);
        ActionLogModel::_()->log("禁用 {$id}，结果", "删除用户");
    }
}