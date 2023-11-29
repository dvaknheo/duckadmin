<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace SimpleBlog\Controller;

use DuckPhp\Core\App;
use DuckPhp\Helper\ControllerHelperTrait;

class Helper
{
    use ControllerHelperTrait;

    public function recordsetUrl($data, $cols_map = [])
    {
        //need more quickly;
        if ($data === []) {
            return $data;
        }
        if ($cols_map === []) {
            return $data;
        }
        $keys = array_keys($data[0]);
        array_walk(
            $keys,
            function (&$val, $k) {
                $val = '{'.$val.'}';
            }
        );
        foreach ($data as &$v) {
            foreach ($cols_map as $k => $r) {
                $values = array_values($v);
                $changed_value = str_replace($keys, $values, $r);
                $v[$k] = __url($changed_value);
            }
        }
        unset($v);
        return $data;
    }
    public function recordsetH($data, $cols = [])
    {
        if ($data === []) {
            return $data;
        }
        $cols = is_array($cols)?$cols:array($cols);
        if ($cols === []) {
            $cols = array_keys($data[0]);
        }
        foreach ($data as &$v) {
            foreach ($cols as $k) {
                $v[$k] = __h($v[$k], ENT_QUOTES);
            }
        }
        return $data;
    }
    public function getUserData()
    {
        $phase = App::Phase();
        try{
        static::User()->checkLogin();
        
        }catch(\Exception $ex){
            App::Phase($phase);
            return [];
        }
        return static::User()->data();
    }
}
