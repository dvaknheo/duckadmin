<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Business;

use DuckPhp\Helper\BusinessHelperTrait;

class Helper
{
    use BusinessHelperTrait;
    public static function ThrowOn($flag,$message,$code)
    {
        return BusinessException::ThrowOn($flag,$message,$code);
    }
}
