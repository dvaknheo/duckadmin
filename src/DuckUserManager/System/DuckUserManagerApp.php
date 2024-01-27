<?php declare(strict_types=1);
/**
 * 这里我们做一下
 */
namespace DuckUserManager\System;

use DuckPhp\DuckPhpAllInOne;
use DuckPhp\Foundation\FastInstallerTrait;

/**
 * 入口类
 */
class DuckUserManagerApp extends DuckPhpAllInOne
{
    use FastInstallerTrait;
    //@override
    public $options = [
        'path' => __DIR__ . '/../',
        
        'ext_options_file_enable' => true,  //使用额外的选项
        'controller_resource_prefix' => 'res/',  // 资源文件前缀
    ];
}