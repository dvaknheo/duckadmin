<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckUserManager\Business;

use DuckPhp\Foundation\SimpleBusinessTrait;
use DuckPhp\Helper\BusinessHelperTrait;

use DuckUserManager\Business\UserBusiness as Helper;
use DuckUserManager\Model\UserModel;

/**
 * 我们偷懒，把 BusinessHelper 集成进这里,基类我们也不要了，毕竟只有一个
 * 绑定异常，
 */
class UserBusiness
{
    use SimpleBusinessTrait; // 单例
    use BusinessHelperTrait; //使用助手函数
    
    public function __construct()
    {
    }
}