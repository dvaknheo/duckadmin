<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */
namespace DuckAdmin\Business;


use DuckAdmin\Model\AdminRoleModel;
use DuckAdmin\Model\RoleModel;
use DuckAdmin\Model\RuleModel;

use DuckPhp\Foundation\ThrowOnableTrait;
use DuckPhp\Helper\BusinessHelperTrait;
use DuckPhp\SingletonEx\SingletonExTrait;
/**
 * 业务基本类，业务程序员的公用代码放在这里
 */
class BaseBusiness
{
    use SingletonExTrait; // 单例
    use BusinessHelperTrait; //使用助手函数
    use ThrowOnableTrait;  //使用 static:: ThrowOn()
	
	public function __construct()
	{
		$this->exception_class = BusinessException::class;
	}
	///////////////////////
}
