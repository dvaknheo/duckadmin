<?php
namespace DuckUser\Test;

use DuckPhp\Foundation\SimpleSingletonTrait;
use DuckPhp\Core\CoreHelper;

class Tester
{
    use SimpleSingletonTrait;
    
    public function getTestList()
    {
$list = <<<EOT
#WEB index
#WEB register
#WEB register name={username}&password=123456&password_confirm=123456
#WEB register name={username}&ssssssssssssssssssssssamename=1
#WEB Home/index
#WEB logout
#WEB index
#WEB login
#WEB login name={username}&password=nolllllllllllllllllllogin
#WEB login name={username}&password=123456
#WEB Home/index
#WEB Home/password
#WEB Home/password oldpassword=123456&newpassword=654321&newpassword_confirm=654321
#WEB Home/password oldpassword=654321&newpassword=123456&newpassword_confirm=123456
#WEB Home/password oldpassword=654321&newpassword=123456&newpassword_confirm=123456

EOT;

        $x = <<<EOT
#WEB index
#WEB register
#WEB register name={username}&password=123456&password_confirm=123456
EOT;
        $prefix = \DuckUser\System\DuckUserApp::_()->options['controller_url_prefix'];
        
        $args = [
            'username' =>'uu1',
        ];
        $args ['static'] = static::class;
        $list = CoreHelper::_()->formatString($list,$args);
        $list = str_replace('#WEB ','#WEB '.$prefix,$list);
        return $list;
    }
    ////[[[[
    protected function getNextInsertId($table)
    {
        
        $database_driver = \DuckUser\System\DuckUserApp::_()->options['database_driver'];
        if($database_driver ==='mysql'){
            $sql = "show table status where Name ='".\DuckUser\System\DuckUserApp::_()->options['table_prefix'] .$table."'";
            $ret = \DuckPhp\Component\DbManager::Db()->fetch($sql)["Auto_increment"];
        }
        if($database_driver ==='sqlite'){
            $sql = "select seq from sqlite_sequence where name = ?";
            $ret = \DuckPhp\Component\DbManager::Db()->fetchColumn($sql,$table);
        }
        return $ret;
        
    }
    private function replace_string($str,$args)
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
