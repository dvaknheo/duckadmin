<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckUser\Business;

use DuckUser\System\ProjectBusiness;

class BaseBusiness extends ProjectBusiness
{
    //其他 Business 类，都调用这个类，而不和 DuckUser\System 发生联系
}
