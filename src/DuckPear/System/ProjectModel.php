<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckPear\System;

use DuckPhp\Foundation\SimpleModelTrait;
use DuckPhp\Helper\ModelHelperTrait;

class ProjectModel
{
    use SimpleModelTrait;
    use ModelHelperTrait;
    
    public static function GetTableByClass($class)
    {
        return static::G()->_GetTableByClass($class);
    }
    public function _GetTableByClass($class)
    {
        // 表前缀要跟着自己，而不是系统
        $this->table_prefix = $table_prefix ?? (\DuckAdmin\System\App::G()->options['table_prefix']??'');
        $t = explode('\\', $class);
        $class = array_pop($t);
        
        $table_name = 'admin_'.strtolower(substr($class,0,-5));
        
        return $table_name;
    }
}
