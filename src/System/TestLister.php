<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */
namespace DuckAdmin\System;

use DuckCoverage\DuckCoverage;
use DuckPhp\Core\App;
use DuckPhp\Foundation\SingletonTrait;


//@codeCoverageIgnoreStart
class TestLister
{
    use SingletonTrait;
    public static function GetTestList()
    {
        return static::_()->_GetTestList();
    }
    public function _GetTestList()
    {
        $static = static::class;
        $flag = DuckCoverage::_()->getFlag();
        foreach (App::_()->options['app'] as $class => $options) {
            $name = $options['controller_url_prefix'] ?? '';
            $name = rtrim($name, '/');
            if ($name === $flag) {
                $list = "PHASE \n";
                if($class == \DuckAdmin\User\System\UserApp::class){
                    $list .= "CALL {$static}@toDefaultAdmin\n";
                } else if($class == \DuckAdmin\SimpleBlog\System\SimpleBlogApp::class) {
                    $list .= "CALL {$static}@toDefaultAdmin\n";
                    $list .= "CALL {$static}@toDefaultUser\n";
                    $list .= "#INCLUDE_CHILD {$class}\n";
                }
                $list .= "#INCLUDE_CHILD {$class}\n";
                return $list;
            }
        }
        return $this->_GetTestListFull();
    }
    public function _GetTestListFull()
    {
        $static = static::class;
        $list = <<<EOT
#INCLUDE_CHILD DuckAdmin\\SingleAdmin\\System\\SingleAdminApp
PHASE 
#INCLUDE_CHILD DuckAdmin\\DemoUsers\\System\\DemoUsersApp
PHASE 
#INCLUDE_CHILD DuckAdmin\\Admin\\System\\AdminApp
PHASE 
#INCLUDE_CHILD DuckAdmin\\Admin_LaravelModel\\System\\LaravelModelApp
PHASE 
#INCLUDE_CHILD DuckAdmin\\Admin_ThinkPHPModel\\System\\ThinkPHPModelApp
PHASE 
CALL {$static}@toDefaultAdmin
#INCLUDE_CHILD DuckAdmin\\User\\System\\UserApp
PHASE 
CALL {$static}@toDefaultAdmin
CALL {$static}@toDefaultUser
#INCLUDE_CHILD DuckAdmin\\SimpleBlog\\System\\SimpleBlogApp
PHASE 

EOT;
        return $list;
    }
    public function toDefaultAdmin()
    {
        $app = App::_();
        $admin_provider = \DuckAdmin\SingleAdmin\System\SingleAdminApp::class;
        //Admin::_(\DuckAdmin\SingleAdmin\System\SingleAdminApp::_());
        $app->saveProviders($admin_provider, null);
    }
    public function toDefaultUser()
    {
        $app = App::_();
        $user_provider = \DuckAdmin\DemoUsers\System\DemoUsersApp::class;
        //User::_(\DuckAdmin\SingleAdmin\System\DemoUsersApp::_());
        $app->saveProviders(null, $user_provider);
    }
}//@codeCoverageIgnoreEnd
