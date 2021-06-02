<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Controller;

use DuckAdmin\System\Controller;

// 我们这里只是偷懒一下啦。 放空类在这，省得 use .
class Base extends Controller
{
    public function __construct()
    {
        // 我们弄个小技巧，不允许直接访问，但我们可以创建一个实例填充，        
        if (Controller::CheckLocalController(self::class, static::class)) {
            return;
        }
        parent::__construct();
    }
}
