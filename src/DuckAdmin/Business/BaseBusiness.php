<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Business;

use DuckAdmin\System\ProjectBusiness;

class BaseBusiness extends ProjectBusiness
{
    protected $exception_class = BusinessException::class;
}
