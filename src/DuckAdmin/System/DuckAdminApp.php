<?php declare(strict_types=1);
/**
 *  入口类
 */
namespace DuckAdmin\System;


use DuckAdmin\Business\InstallBusiness;
use DuckAdmin\Controller\ExceptionReporter;
use DuckPhp\Core\Console;
use DuckPhp\Core\Route;
use DuckPhp\DuckPhp;

/**
 * 入口类
 */
class DuckAdminApp extends DuckPhp
{
   
    //@override
    public $options = [    
        'path' => __DIR__ . '/../',
        'namespace' => 'DuckAdmin',
        'controller_resource_prefix' => 'res/',  // 资源文件前缀
        'controller_method_prefix' => '',
        
        'exception_for_business'  => BusinessException::class,
        'exception_for_controller'  => ControllerException::class,
        'exception_reporter' =>  ExceptionReporter::class,
        
        'class_admin'=> Admin::class,
        
        'database_driver'=>'mysql',
        'need_install'=>true,
        'cli_command_with_fast_installer' => true,
        
        'table_prefix' =>'wa_'
        
    ];
    protected function onPrepare()
    {
        //默认的路由不符合我们这次的路由，换
        Route::_(ProjectRoute::_());
        parent::onPrepare();
    }
    protected function onInited()
    {
        parent::onInited();
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
                $this->doInstall($input_options['username'],$input_options['password'],$input_options['password_confirm']);
            }catch(\Exception $ex){
                echo "----\n";
                echo $ex->getMessage();
                echo "\n";
                continue;
            }
            break;
        };
    }
    public function doInstall($username,$password,$password_confirm)
    {
        InstallBusiness::_()->install($username,$password,$password_confirm);
    }
}
