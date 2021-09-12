<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdminDemo\Controller;

use DuckAdminDemo\System\ProjectController;
use DuckAdminDemo\System\App;

class Base extends ProjectController
{
    public function __construct()
    {
        self::CheckRunningController(self::class,static::class);

        parent::__construct();
    }
}