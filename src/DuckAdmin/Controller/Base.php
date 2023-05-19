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

    /**
     * 数据限制
     * 例如当$dataLimit='personal'时将只返回当前管理员的数据
     * @var string
     */
    protected $dataLimit = null;


    public function __construct()
    {
        $controller = get_class($this);
        $action = AdminAction::getRouteCallingMethod();
		AdminAction::G()->checkAccess($controller,$action);
		
		return;
   
        // 我们弄个小技巧，不允许直接访问，但我们可以创建一个实例填充，
        //if (static::CheckRunningController(self::class, static::class)) {
        //    return;
        //}
        //parent::__construct();
		//我们检查安装？
    }
	
}
