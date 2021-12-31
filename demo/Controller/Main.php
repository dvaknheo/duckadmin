<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdminDemo\Controller;

use DuckAdminDemo\Business\DemoBusiness;
use DuckAdminDemo\Controller\Base as C;

class Main extends Base
{
    public function index()
    {
        C::Show(get_defined_vars(), 'main');
    }
}
