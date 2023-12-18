<?php declare(strict_types=1);
/**
 * 这里我们做一下
 */
namespace DuckUserManager\System;

use DuckPhp\DuckPhpAllInOne;

/**
 * 入口类
 */
class DuckUserManagerApp extends DuckPhpAllInOne
{
    //@override
    public $options = [
        'ext_options_file_enable' => true,  //使用额外的选项
        'controller_resource_prefix' => 'res/',  // 资源文件前缀
    ];
    public function __construct()
    {
        $this->options['path'] = dirname(__DIR__).'/';
        parent::__construct();
    }
}