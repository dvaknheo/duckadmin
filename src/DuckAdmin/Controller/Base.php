<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Controller;

use DuckAdmin\System\ProjectController;

/**
 * 我们这里只是偷懒一下啦。 放空类在这，省得 use .
 * 因为 C 也是这里。 要重载这里的动态方法，要用 Base::G(MyBase::G()); 而不是 XController::G(MyBase::G());
 */
class Base extends ProjectController
{


    /**
     * 无需登录及鉴权的方法
     * @var array
     */
    protected $noNeedLogin = [];

    /**
     * 需要登录无需鉴权的方法
     * @var array
     */
    protected $noNeedAuth = [];

    public function __construct()
    {
		AdminAction::G()->initController(static::class);	
    }
}
