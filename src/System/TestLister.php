<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */
namespace DuckAdmin\System;

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
{static}@toDefaultAdmin
#INCLUDE_CHILD DuckAdmin\\User\\System\\UserApp
PHASE 
{static}@toDefaultAdmin
{static}@toDefaultUser
#INCLUDE_CHILD DuckAdmin\\SimpleBlog\\System\\SimpleBlogApp
PHASE 

EOT;
        return $list;
    }
    public function toDefaultAdmin()
    {
        $app = App::_();
        $admin_provider = \DuckAdmin\SingleAdmin\System\SingleAdminApp::class;
        $app->saveProviders($admin_provider, $user_provider);
    }
    public function toDefaultUser()
    {
        $app = App::_();
        $user_provider = \DuckAdmin\DemoUsers\System\DemoUsersApp::class;
        $app->saveProviders($admin_provider, $user_provider);
    }
}//@codeCoverageIgnoreEnd
