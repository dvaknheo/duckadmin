<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\System;

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
    public static function _GetTableByClass($class)
    {
        $this->table_prefix = $table_prefix ?? (App::G()->options['table_prefix']??'');
        $t = explode('\\', $class);
        $class = array_pop($t);
        
        $table_name = 'admin_'.strtolower(substr($class,0,-5));
        $this->table_name = $this->table_name ??(($table_name==='admin_admin')? 'admin' : $table_name);
        
        return $table_name;
    }
}
