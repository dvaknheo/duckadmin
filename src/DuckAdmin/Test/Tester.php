<?php
namespace DuckAdmin\Test;

use DuckPhp\Foundation\SimpleSingletonTrait;
use DuckAdmin\System\DuckAdminApp;
use Demo\Tester\MyCoverageBridge;
use \DuckPhp\Component\DbManager;
class Tester
{
    use SimpleSingletonTrait;

    public function beginTest()
    {
        //
    }
    public function endTest()
    {
        //
    }
    public function preCurl($ch,$name)
    {
        __var_log(__METHOD__);
    }
    public function postCurl($ch,$name)
    {
        __var_log(__METHOD__);
    }
    public function preWebCall()
    {
        __var_log(__METHOD__);
    }
    public function postWebCall()
    {
        __var_log(__METHOD__);
    }
    public static function localCall()
    {
        __var_log(__METHOD__);
    }
    public function getTestList2()
    {
        $list = <<<EOT
#WEB index
#SETWEB AJAX
#WEB account/dashboard
#WEB account/dashboard
#WEB index
#WEB account/logout
#SETWEB AJAX
#WEB account/logout
#WEB account/login username=admin&password=123456&captcha=7268
#SETWEB OPTIONS
#WEB admin/index?diffname

EOT;
        $static_class = static::class;
        $args = [
            'static' => $static_class,
        ];
        $list = $this->replace_string($list,$args);
        $prefix = DuckAdminApp::_()->options['controller_url_prefix'];
        $list = str_replace('#WEB ','#WEB '.$prefix,$list);

        return $list;
    }
    public function testGetCurrentAdminId()
    {
        try{
            $v1 = \DuckAdmin\Controller\AdminAction::_()->getCurrentAdminName();
            $v2 = \DuckAdmin\Controller\AdminAction::_()->getCurrentAdminName();
            \DuckAdmin\Controller\AdminAction::_()->checkAccess(__CLASS__,__FUNCTION__);
        }catch(\Exception $ex){
            //return;
        }
        try{
            \DuckAdmin\Controller\AdminAction::_()->checkAccess('noexist',__FUNCTION__);
        }catch(\Exception $ex){
            //return;
        }
    }
    public function getTestList()
    {
        $list = <<<EOT
#CALL {static}@beginTest
##SETWEB preCurl preWebCall postWebCall postCurl
#SETWEB AJAX
#WEB account/dashboard
#WEB account/dashboard
#WEB account/login
#WEB account/login username=admin&password=123456&captcha=7268
#WEB account/info
#WEB account/dashboard
#SETWEB OPTIONS
#WEB account/info?seetheoptions

#SETWEB _ _ {static}@testGetCurrentAdminId
#WEB index?s=testGetCurrentAdminId

#WEB account/index
#WEB account/captcha
#WEB account/password
#WEB account/password old_password=123456&password=654321&password_confirm=654321
#WEB account/password old_password=654321&password=123456&password_confirm=123456
#WEB account/update nickname=%E8%B6%85%E7%BA%A7%E7%AE%A1%E7%90%86%E5%91%98&email=112233a&mobile=22244b
#WEB account/logout
#SETWEB AJAX
#WEB account/logout

#WEB account/dashboard
#WEB account/login username=admin&password=123456&captcha=7268
#SETWEB OPTIONS
#WEB admin/index?seetheoptions

#WEB admin/index
#WEB admin/select
#WEB admin/select?format=select
#WEB admin/insert
#WEB admin/update
#WEB admin/delete
#WEB admin/insert roles=1&username=admin{new_admin_id}&nickname=the_admin{new_admin_id}&password=123456&email=youxiang{new_admin_id}&mobile=shouji{new_admin_id}
#WEB admin/update roles=-1&username=admin{new_admin_id}&nickname=the_admin_new{new_admin_id}&password=&email=xyouxiang{new_admin_id}&mobile=xshouji{new_admin_id}&id={new_admin_id}
#WEB account/login username=admin{new_admin_id}&password=123456&captcha=7268
#WEB admin/insert

#WEB account/login username=admin&password=123456&captcha=7268

#CALL {static}::BeginConfigBusiness
#WEB account/login username=admin&password=123456&captcha=7268
#WEB config/index
#WEB config/get
#WEB config/update
#WEB config/update bad%5Bskip%5D=on&tab%5BkeepState%5D=on&tab%5Bsession%5D=on&tab%5Bmax%5D=30&tab%5Btitle%5D=%E4%BB%AA%E8%A1%A8%E7%9B%98&tab%5Bhref%5D=index%2Fdashboard&tab%5Bid%5D=0&tab%5Bindex%5D%5Bid%5D=0&tab%5Bindex%5D%5Bhref%5D=index%2Fdashboard&tab%5Bindex%5D%5Btitle%5D=%E4%BB%AA%E8%A1%A8%E7%9B%98&colors%5B6%5D=bad
#CALL {static}::EndConfigBusiness

##WEB account/login username=admin&password=123456&captcha=7268
#WEB role/index
#WEB role/select
#WEB role/select?format=tree
#WEB role/rules
#WEB role/insert
#WEB role/update
#WEB role/insert pid=1&name=myrole{new_role_id}a&rules=1
#WEB role/update pid=1&name=myrol2e{new_role_id}&rules=1&id={new_role_id}

##WEB account/login username=admin&password=123456&captcha=7268
#WEB rule/index
#WEB rule/select
#WEB rule/get
#WEB rule/permission
#WEB rule/insert
#WEB rule/update
#WEB rule/delete
#WEB rule/insert title=biaoti{new_rule_id}&key=biaozhi{new_rule_id}&pid=&href=&icon=layui-icon-login-wechat&type=1&weight=0
#WEB rule/update title=biaoti{new_rule_id}a&key=biaozhi{new_rule_id}x&pid=&href=&icon=layui-icon-login-wechat&type=1&weight=0&id={new_rule_id}
#WEB admin/delete id={new_admin_id}
#WEB role/delete id={new_role_id}
#WEB rule/delete id={new_rule_id}

#WEB 
#WEB account/logout
#WEB index

#CALL {static}@beginTest

EOT;
        $last_phase = DuckAdminApp::Phase(DuckAdminApp::class);
        $new_admin_id = $this->getNextInsertId('admins');
        $new_role_id = $this->getNextInsertId('roles');
        $new_rule_id = $this->getNextInsertId('rules');
        
        //$list = "#WEB account/logout  AJAX\n";
        //$list ="#CALL {static}::KickTestDirectory\n";
        $static_class = static::class;
        $args = [
            'new_admin_id'=>$new_admin_id,
            'new_role_id'=>$new_role_id,
            'new_rule_id'=>$new_rule_id,
            'static' => $static_class,
        ];
        $list = $this->replace_string($list,$args);
        
        $prefix = DuckAdminApp::_()->options['controller_url_prefix'];
        $list = str_replace('#WEB ','#WEB '.$prefix,$list);
        DuckAdminApp::Phase($last_phase);
        return $list;
    }
    public static function BeginConfigBusiness()
    {
        $last_phase = DuckAdminApp::Phase(DuckAdminApp::class);
        static::_()->_BeginConfigBusiness();
        DuckAdminApp::Phase($last_phase);
    }
    public function _BeginConfigBusiness()
    {
        $table = DuckAdminApp::_()->options['table_prefix'] .'options';
        $sql = "truncate `'TABLE'`";
        $sql = str_replace("`'TABLE'`",$table,$sql);
        $data = DbManager::Db()->execute($sql);
        /*
        if(!empty($data)){
            $sql = "update `'TABLE'` set name ='system_config_bak' where name = 'system_config'";
            $sql = str_replace("`'TABLE'`",$table,$sql);
            $data = DbManager::Db()->execute($sql);
        }
        */
    }
    public static function EndConfigBusiness()
    {
        $last_phase = DuckAdminApp::Phase(DuckAdminApp::class);
        static::_()->_EndConfigBusiness();
        DuckAdminApp::Phase($last_phase);
    }
    public function _EndConfigBusiness()
    {
        $table = DuckAdminApp::_()->options['table_prefix'] .'options';
        /*
        $sql = "select * from `'TABLE'` where name='system_config'";
        $sql = str_replace("`'TABLE'`",$table,$sql);
        $data = DbManager::Db()->fetch($sql);
        if(empty($data)){
            $sql = "update `'TABLE'` set name ='system_config' where name = 'system_config_bak'";
            $sql = str_replace("`'TABLE'`",$table,$sql);
            $data = DbManager::Db()->execute($sql);
        }
        */
    }
    ////////////////
    private function getNextInsertId($table)
    {
        $database_driver = DuckAdminApp::_()->options['database_driver'];
        if($database_driver ==='mysql'){
            $sql = "show table status where Name ='".\DuckAdmin\System\DuckAdminApp::_()->options['table_prefix'] .$table."'";
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
    public function runExtBusiness()
    {
        ////[[[[
            $admin_id = 2;
            $op_id = $admin_id;
            \DuckAdmin\Business\AccountBusiness::_()->canAccess($admin_id,\DuckAdmin\Controller\AdminController::class,'index');
            \DuckAdmin\Business\AdminBusiness ::_()->showAdmins($op_id,[]);
            \DuckAdmin\Business\RoleBusiness ::_()->tree($op_id,3);
            \DuckAdmin\Business\RuleBusiness ::_()->get($op_id,[0,1]);
            \DuckAdmin\Business\RuleBusiness ::_()->permission($op_id);
            

        ////]]]]
        // CommonService, Tree
    }
    public function runExtModel()
    {
        ////[[[[

        ////]]]]
    }
    public function runAllExtract()
    {
        // 这里是 System 的其他测试
    }
    //// start ////
    
}
