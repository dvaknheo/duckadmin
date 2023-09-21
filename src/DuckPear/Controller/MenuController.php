<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckPear\Controller;

use DuckPear\Controller\Base as C;
use DuckPear\Business\AdminBusiness;
/**
 * 菜单管理
 */
class MenuController extends Base
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
