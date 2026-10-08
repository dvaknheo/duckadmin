<?php declare(strict_types=1);
/**
 * DuckAdmin DemoUsers - 预设用户系统
 * 不使用数据库、不使用安装系统;登录限定 demo_users 数组中的用户
 */
namespace DuckAdmin\System;

use DuckPhp\Foundation\SingletonTrait;
use DuckPhp\Core\App;

//@codeCoverageIgnoreStart
abstract class TestListWithAuthBase
{
    use SingletonTrait;
    public static function GetTestList(): string
    {
        return static::_()->_GetTestList();
    }
    public function _GetTestList(): string
    {
        $cmd = (string)(App::_()->options['duckcoverage_test_lister_parameter'] ?? '');
        unset(App::_()->options['duckcoverage_test_lister_parameter']);
        switch ($cmd) {
            case 'login':
                return $this->_GetTestListForLogin();
            case 'logout':
                return $this->_GetTestListForLogout();
            case 'clean':
                return $this->_GetTestListForClean();
            default:
                return $this->_GetTestListFull();
        }
    }
    public function _GetTestListForLogin(): string
    {
        throw new \Exception('Not implemented');
    }
    public function _GetTestListForLogout(): string
    {
        throw new \Exception('Not implemented');
    }
    public function _GetTestListForClean(): string
    {
        throw new \Exception('Not implemented');
    }
    public function _GetTestListFull(): string
    {
        throw new \Exception('Not implemented');
    }

}//@codeCoverageIgnoreEnd
