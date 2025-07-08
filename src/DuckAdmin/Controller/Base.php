<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Controller;
use DuckPhp\Foundation\SimpleControllerTrait;
use DuckPhp\GlobalAdmin\AdminControllerInterface;
use DuckAdmin\Business\RuleBusiness;

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
        
        $types = [0, 1];
            $admin_id = Helper::AdminId();
            $menu_data = RuleBusiness::_()->get($admin_id,$types);
            
            Helper::assignViewData('data_menu',$menu_data);
            //Helper::setViewHeadFoot($head_file = null, $foot_file = null)
    }
}
