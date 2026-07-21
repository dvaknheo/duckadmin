<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Controller;
use DuckPhp\Foundation\ControllerTrait;
use DuckPhp\GlobalAdmin\AdminControllerInterface;
use DuckAdmin\Business\RuleBusiness;

class Base implements AdminControllerInterface
{
    use ControllerTrait;
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
            AdminAction::_()->checkAccess();
            return;
        }
            

        AdminAction::_()->checkAccess();
        
        Helper::setViewHeadFoot('_sys/header', '_sys/footer');
        $types = [0, 1];
        try{
            $admin_id = Helper::AdminId(); //有几个不需要 admin_id 的
        }catch(\Exception $ex){ return;}
        $menu_data = RuleBusiness::_()->get($admin_id,$types);
        
        Helper::assignViewData('data_menu',$menu_data);
        
    }
}
