<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Admin\Controller;

use DuckPhp\Foundation\SingletonTrait;
use DuckPhp\Core\App;
use DuckPhp\GlobalUser\UserException;

use DuckAdmin\Admin\Business\AdminBusiness;
use DuckAdmin\Admin\Model\PermissionModel;

class AdminAction
{
    use SingletonTrait;
    public function __construct()
    {
    }

}
