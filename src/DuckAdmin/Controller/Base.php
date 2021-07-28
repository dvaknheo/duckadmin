<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Controller;

use DuckAdmin\System\ProjectController;

// 我们这里只是偷懒一下啦。 放空类在这，省得 use .
// 因为 C 也是这里。 要重载这里的动态方法，要用 Base::G(MyBase::G()); 而不是 Controller::G(MyBase::G());
class Base extends ProjectController
{
    public function __construct()
    {
        // 我们弄个小技巧，不允许直接访问，但我们可以创建一个实例填充，        
        if (static::CheckLocalController(self::class, static::class)) {
            return;
        }
        parent::__construct();
    }
}
