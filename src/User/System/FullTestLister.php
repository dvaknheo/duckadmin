<?php
namespace DuckAdmin\User\System;

use DuckPhp\Component\DbManager;
use DuckPhp\Core\CoreHelper;
use DuckPhp\Foundation\SingletonTrait;

use DuckAdmin\User\System\UserApp;
use DuckAdmin\User\Model\UserModel;
use DuckAdmin\User\Controller\UserAction;

class FullTestLister
{
    use SingletonTrait;
    
    public static function BeforeTest()
    {
        static::_()->_BeforeTest();
    }
    public function _BeforeTest()
    {
        $table = UserModel::_()->table();
        $sql = "delete from $table where username = ?";
        $ret = DbManager::Db()->execute($sql,'user_test');
    }
    public static function AfterTest()
    {
        static::_()->_AfterTest();
    }
    public function _AfterTest()
    {
        return;
    }
    public function getTestList()
    {
        // 头部指令:#PHASE / #URL_PREFIX,由子 Tester 在自身 phase 下生成
        $str = ''; // '#PHASE '.UserApp::_()->getThisPhaseName()."\n";
$list = <<<EOT
PHASE {phase}
WEB index
WEB register
WEB register name={username}&password=123456&password_confirm=123456
WEB register name={username}&ssssssssssssssssssssssamename=1
WEB Home/index?_r=1
WEB logout
WEB index
WEB login
WEB login name={username}&password=nolllllllllllllllllllogin
WEB login name={username}&password=123456
WEB Home/index?_r=2
WEB Home/password
WEB Home/password oldpassword=123456&newpassword=654321&newpassword_confirm=654321
WEB Home/password oldpassword=654321&newpassword=123456&newpassword_confirm=123456
WEB Home/password oldpassword=654321&newpassword=123456&newpassword_confirm=123456
EOT;
$list = <<<EOT
PHASE {phase}
WEB register
EOT;

        $prefix = '/'.UserApp::_()->options['controller_url_prefix'];
        $phase = UserApp::_()->getThisPhaseName()."\n";

        $args = [
            'phase' => $phase,
            'username' =>'user_test',
        ];
        $args ['static'] = static::class;
        $list = $this->replace_string($list,$args);
        $list = str_replace('WEB ','WEB '.$prefix,$list); // 按行替换，应该用正则
        return $str.$list;
    }
    ////[[[[
    protected function getNextInsertId($table)
    {
        
        $database_driver = UserApp::_()->options['database_driver'];
        if($database_driver ==='mysql'){
            $sql = "show table status where Name ='".UserApp::_()->options['table_prefix'] .$table."'";
            $ret = DbManager::Db()->fetch($sql)["Auto_increment"];
        }
        if($database_driver ==='sqlite'){
            $sql = "select seq from sqlite_sequence where name = ?";
            $ret = DbManager::Db()->fetchColumn($sql,$table);
        }
        return $ret;
        
    }
    protected function replace_string($str,$args)
    {
        if (empty($args)) {
            return $str;
        }
        $a = [];
        foreach ($args as $k => $v) {
            $a["{".$k."}"] = $v;
        }
        
        $ret = str_replace(array_keys($a), array_values($a), $str);
        
        return $ret;
    }
    
}
