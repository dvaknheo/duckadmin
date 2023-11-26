<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\System;

use DuckPhp\Foundation\SimpleBusinessTrait;
/**
 * 这里是给外部调用的 服务类，一般是用于
 * 这里调用各种业务服务，你的 Business 业务层调用这里的静态方法
 * 当然，如果你认为已经没法满足你了，修改 Business 的实现也行
 */
class ServiceApi
{
    use SimpleBusinessTrait;
    
}