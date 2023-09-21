<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Controller;

use DuckAdmin\Controller\Base as C;
use DuckAdmin\Business\AdminBusiness;
/**
 * 菜单管理
 */
class Menu extends Base
{
    /**
     * 管理员
     */
    public function index()
    {
        //C::SetMenu('aa/bb');
        $data = [];
        C::Show($data,'menu/index');
    }
}
