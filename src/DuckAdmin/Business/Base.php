<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */
namespace DuckAdmin\Business;

use DuckPhp\Helper\BusinessHelperTrait;
use DuckPhp\Foundation\SimpleBusinessTrait;
/**
 * 业务基本类，业务程序员的公用代码放在这里
 */
class Base
{
    use SimpleBusinessTrait; // 单例
    use BusinessHelperTrait; //使用助手函数
    
    public function __construct()
    {
    }
    ///////////////////////
}
