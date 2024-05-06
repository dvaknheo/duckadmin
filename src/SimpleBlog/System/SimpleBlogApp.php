<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace SimpleBlog\System;

use DuckPhp\Core\EventManager;
use DuckPhp\DuckPhp;
use DuckPhp\FastInstaller\FastInstaller;
use SimpleBlog\Controller\ExceptionReporter;

class SimpleBlogApp extends DuckPhp
{
    public $options = [
        'path' => __DIR__ . '/../',
        'exception_reporter' =>  ExceptionReporter::class,
        'exception_for_business'  => ProjectException::class,
        'exception_for_controller'  => ProjectException::class,
        'rewrite_map' => [
            '~article/(\d+)/?(\d+)?' => 'article?id=$1&page=$2',
        ],
        'cli_command_classes' => [FastInstaller::class],
        
        'database_driver'=>'sqlite',
        
        'install_options'=>[
            'app_a'=>'111',
        ],
        //install_input_validators
        'install_input_desc' => <<<EOT
url prefix: [{controller_url_prefix}]
resource prefix: [{controller_resource_prefix}]
zzzzz [{app_a}]
zzzzz [{app_b}]
//就是说duck可以很好的解决应用间的复用和嵌套问题，而且对应用的修改和拓展也是无侵害的
----
EOT
,
        'app_b'=> '222',
    ];
    public function onInit()
    {
        //EventManager::OnEvent([static::class,'OnInstalled'],[static::class,'OnInstall']);
    }
    public function callbackForFastInstallerDoInstall($input_options = [], $ext_options = [], $app_options = [])
    {
        //
        return true;
    }
}
