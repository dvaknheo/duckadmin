<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckUser\Controller;

use DuckUser\System\ProjectController;
use DuckPhp\Foundation\SimpleControllerTrait;
use DuckPhp\Helper\ControllerHelperTrait;

// 基类，其他类都调用这个类，而不和 DuckUser\System 联系
class Base extends ProjectController
{
    public function __construct()
    {
        if(static::class === self::class){
            static::Exit404();
        }
    }
    
    use SimpleControllerTrait;
    use ControllerHelperTrait;
    
    //// 以下是专有助手方法 ////
    protected static function GoHome()
    {
        return static::G()->_GoHome();
    }
    protected function _GoHome()
    {
        static::ExitRouteTo(App::G()->options['home_url']);
    }
}