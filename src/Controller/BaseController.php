<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Controller;

use DuckAdmin\Helper\ControllerHelper as C;
use DuckAdmin\App\SingletonEx;
use DuckAdmin\App\App;

class BaseController
{
    use SingletonEx;
    
    
    public function __construct()
    {
        if (static::class === self::class) {
            C::Exit404();
        }
        
        SessionService::G()->checkLogin();
        //AdminSerivce::G()->record();
        C::setViewHeadFoot('header','footer');
        $this->initialize();
    }
    // 初始化
    protected function initialize()
    {
    }
    
    public function foo()
    {
        var_dump(DATE(DATE_ATOM));
    }
    ///////////////////////////////////
    protected static function Show($data,$view=null)
    {
        App::Show($data,$view);
    }
}
