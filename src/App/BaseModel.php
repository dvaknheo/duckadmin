<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\App;

use DuckAdmin\App\SingletonExTrait;

class BaseModel
{
    use SingletonExTrait;
    protected $table;
    protected function prepare($sql)
    {
        return str_replace('TABLE',$this->table,$sql);
    }
}
