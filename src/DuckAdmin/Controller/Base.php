<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Controller;
use DuckPhp\Foundation\SimpleControllerTrait;
use DuckPhp\GlobalAdmin\AdminControllerInterface;

class Base implements AdminControllerInterface
{
    use SimpleControllerTrait;
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
        $this->initController();
    }
    protected function initController()
    {
        if(Helper::IsAjax()){
            Helper::assignExceptionHandler(\Exception::class,[Helper::class,'ShowException']);
        }
        AdminAction::_()->checkAccess();
        
        //$data = RuleBusiness::_()->get($admin_id,$types);
        //SetHeaderFooter();
        //SetViewData();
    }
}
