<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckAdminDemo\System;

use DuckPhp\Foundation\SimpleModelTrait;
use DuckPhp\Helper\ModelHelperTrait;
use DuckPhp\SingletonEx\SingletonExTrait;

class ProjectModel
{
    use SingletonExTrait;
    use SimpleModelTrait;
    use ModelHelperTrait;
    
    public function __construct()
    {
        //$this->table_prefix = App::G()->getTablePrefix();
    }
}
