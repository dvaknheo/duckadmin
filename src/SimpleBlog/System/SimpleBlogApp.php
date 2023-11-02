<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace SimpleBlog\System;

use DuckPhp\DuckPhp;
use DuckPhp\Component\DbManager;
use DuckPhp\Ext\RouteHookRewrite;
use SimpleAuth\Api\SimpleAuthPlugin;
use SimpleBlog\Business\AdminBusiness;

class App extends DuckPhp
{
    //@override
    public $options = [       
        'error_404' =>'_sys/error-404',
        'error_500' => '_sys/error-exception',
        
        'ext' => [
            RouteHookRewrite::class => true,// 我们需要 重写 url
        ],        
        //url 重写
        'rewrite_map' => [
            '~article/(\d+)/?(\d+)?' => 'article?id=$1&page=$2',
        ],
        
        'table_prefix' => '',
        'session_prefix' => '',
    ];
    ////[[[[
    public function install($parameters)
    {
        return $this->getInstaller()->install($parameters);
    }
    ////////////////////////
    protected function getInstaller($options=[])
    {
        $options = [
            'installer_table_prefix' => $this->options[ 'duckadmin_table_prefix'],
        ];
        
        $installed = $this->isInstalled();
        
        $has_database = (static::Setting('database') ||  static::Setting('database_list')) ? true : false;
        $options['installer_has_database'] = $has_database;
        
        return Installer::G()->init($options,$this);
    }
    ////]]]]
    ////////////////////////////
    
    /** reset SimpleBlog password */
    public function command_reset_password()
    {
        $new_pass = AdminBusiness::G()->reset();
        echo 'new password: '.$new_pass;
        echo PHP_EOL;
    }
    /** Install SimpleBlog */
    public function command_install()
    {
        echo "Welcome to Use SimpleBlog installer  --force  to force install\n";
        $parameters =  static::Parameter();
        if(count($parameters)==1 || ($parameters['help'] ?? null)){
            // echo "--force  to force install ;";
            //return;
        }

        $this->install($parameters);
        $new_pass = AdminBusiness::G()->reset('123456');
        echo 'new password: '.$new_pass;
        echo PHP_EOL;
        
        echo "Done \n";
    }
}
