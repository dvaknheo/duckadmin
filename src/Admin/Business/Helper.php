<?php declare(strict_types=1);
/**
 * DuckPhp Admin System
 */
namespace DuckAdmin\Admin\Business;

use DuckPhp\Foundation\Business\BusinessHelper;
use DuckPhp\Ext\PermissionMenu;

class Helper extends BusinessHelper 
{
    /**
     * Summary of permissionMenu
     * @return PermissionMenu
     */
    public function permissionMenu()
    {
        return PermissionMenu::_();
    }
}
