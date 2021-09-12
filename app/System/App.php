<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdminDemo\System;

use DuckPhp\DuckPhp;
//use DuckPhp\Component\AppPluginTrait;

class App extends DuckPhp
{
    //@override
    public $options = [
        //'path_info_compact_enable' => false,        
    ];
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * 快速开始
    */
    public function command_go()
    {
        // 检查有没有设置，如果没有那么那么进入安装模式
        //
        
        // 然后进入 workerman 的项目启动
        
        //
    }
}
