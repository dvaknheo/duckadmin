<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckPear\Business;

use DuckPear\System\ProjectBusiness;
/**
 * 业务基本类，业务程序员的公用代码放在这里
 */
class BaseBusiness extends ProjectBusiness
{
    protected $exception_class = BusinessException::class;  // 这里要调一下？
}
