<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Controller;

use DuckPhp\Helper\ControllerHelperTrait;

class Helper
{
    use ControllerHelperTrait;

    public static function Success($data = [],$count = null)
    {
        return static::_()->_Success($data, $count);
    }
    public function _Success($data = [],$count = null)
    {
        if(is_null($count)){
            static::ShowJson(['code' => 0, 'data' => $data, 'msg' => 'ok']);
        }else{
            static::ShowJson(['code' => 0, 'msg' => 'ok', 'count' => $count, 'data' => $data]);
        }
    }
    public static function ShowException($ex)
    {
        return static::_()->_ShowException($ex);
    }
    public function _ShowException($ex)
    {
        $code = $ex->getCode();
        $msg = $ex->getMessage();
        if(!$code){$code = -1;}
        static::ShowJson(['code' => $code, 'msg' => $msg, 'type' => 'error']);
    }
}