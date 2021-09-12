<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Business;

use DuckAdmin\System\ProjectBusiness;
/**
 * 业务基本类，业务程序员的公用代码放在这里
 */
class BaseBusiness extends ProjectBusiness
{
    protected $exception_class = BusinessException::class;
}
