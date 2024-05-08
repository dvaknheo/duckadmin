<?php declare(strict_types=1);
/**
 * 这里我们做一下
 */
namespace DuckAdmin\System;

use DuckAdmin\Business\InstallBusiness;
use DuckPhp\Core\Console;
use DuckPhp\Core\Route;
use DuckPhp\DuckPhp;
use DuckPhp\FastInstaller\FastInstaller;

/**
 * 入口类
 */
class DuckAdminApp extends DuckPhp
{
   
    //@override
    public $options = [    
        'path' => __DIR__ . '/../',
        'controller_resource_prefix' => 'res/',  // 资源文件前缀
        'database_driver'=>'mysql',
        'controller_method_prefix' => '',
        'class_admin'=> Admin::class,
        
        'cli_command_classes' => [FastInstaller::class],
        
        //----
        //'install_input_desc'=>'input [{abc}]',
        //'install_default_options'=>['username'=>'admin','password'=>'123456','password_confirm'=>''],
    ];
    protected function onPrepare()
    {
        //默认的路由不符合我们这次的路由，换
        Route::_(ProjectRoute::_());
        parent::onPrepare();
    }
    protected function onInit()
    {
    }
    public function onInstall()
    {
        $desc =<<<EOT
设置超级管理员：
----
默认管理员：[{username}]
默认密码  ：[{password}]
重复密码  ：[{password_confirm}]

EOT;
        $input_options = [
            'username'=>'admin',
            'password'=>'123456',
            'password_confirm'=>'123456',
        ];
        while(true){
            try{
                $input_options = Console::_()->readLines($input_options, $desc);
                $this->doInstallStep2($input_options['username'],$input_options['password'],$input_options['password_confirm']);
            }catch(\Exception $ex){
                echo "----\n";
                echo $ex->getMessage();
                echo "\n";
                continue;
            }
            break;
        };
        
        return true;
    }
    public function doInstallStep2($username,$password,$password_confirm)
    {
        InstallBusiness::_()->step2($username,$password,$password_confirm);
    }
}
