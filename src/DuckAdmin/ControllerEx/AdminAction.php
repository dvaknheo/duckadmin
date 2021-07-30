<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\ControllerEx;

use DuckAdmin\Business\AdminBusiness;
use DuckAdmin\ControllerEx\AdminSession;
use DuckAdmin\System\ProjectController;

class AdminAction extends ProjectController
{
    public function __construct()
    {
    }
    public function doCheckPermission()
    {
        $admin = AdminSession::G()->getCurrentAdmin();
        $path_info = static::getPathInfo();
        $flag = AdminBusiness::G()->checkPermission($admin,$path_info);
        return $flag;
    }
}