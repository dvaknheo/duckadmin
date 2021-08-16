<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\System;

use DuckPhp\Foundation\SimpleModelTrait;
use DuckPhp\Helper\ModelHelperTrait;
use DuckPhp\SingletonEx\SingletonExTrait;

class ProjectModel
{
    use SingletonExTrait;
    use ModelHelperTrait;
    use SimpleModelTrait;
    
    public static function GetTableByClass($class)
    {
        return static::G()->_GetTableByClass($class);
    }
    public static function _GetTableByClass($class)
    {
        $t = explode('\\', $class);
        $class = array_pop($t);
        
        $table_name = 'admin_'.strtolower(substr($class,0,-5));
        $table_name = ($table_name==='admin_admin')? 'admin' : $table_name;
        $table_name = App::Setting('duckadmin_pre_prefix').$table_name;
        
        return $table_name;
    }
}