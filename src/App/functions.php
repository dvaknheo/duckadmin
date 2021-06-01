<?php
/**
 * 这个文件表示提供的函数用于调用
 */
use DuckAdmin\App\DuckAdmin;

if (function_exists('duckadmin_res')) {

    /**
 * 返回资源地址
 * @param type $path
 */
function duckadmin_res($path)
{
    return DuckAdmin::Url($path);
}

}