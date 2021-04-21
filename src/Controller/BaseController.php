<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Controller;

use DuckAdmin\App\BaseController as Base;
use DuckAdmin\Helper\ControllerHelper as C;



class BaseController extends Base
{
    public function __construct()
    {
        if (static::class === self::class) {
            C::Exit404();
        }
        parent::__construct();
    }
    protected function initialize()
    {
        //
    }
}
