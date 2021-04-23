<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\App;

use DuckAdmin\App\ControllerHelper as C;
use DuckAdmin\App\App;

class BaseController
{
    use SingletonExTrait;
    public function __construct()
    {
        $this->initialize();
    }
    // 初始化
    protected function initialize()
    {
        C::setViewHeadFoot('header','footer');
    }
    ///////////////////////////////////
    protected static function Show($data,$view=null)
    {
        App::Show($data,$view);
    }
    protected static function WrapException()
    {
        //
    }
}
