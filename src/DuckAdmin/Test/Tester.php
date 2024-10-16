<?php
namespace DuckAdmin\Test;

use DuckPhp\Foundation\Helper;
use DuckPhp\Component\DbManager;
use DuckPhp\Core\PhaseContainer;
use DuckPhp\Foundation\SimpleSingletonTrait;
use DuckAdmin\System\DuckAdminApp;
use Demo\Tester\MyCoverageBridge;


class Tester
{
    use SimpleSingletonTrait;

    public function beginTest()
    {
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

    public function testGetCurrentAdminId()
    {
        try{
            $v1 = \DuckAdmin\Controller\AdminAction::_()->id();
            $v2 = \DuckAdmin\Controller\AdminAction::_()->name();
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
#WEB rule/insert title=biaoti{new_rule_id}&key=biaozhi{new_rule_id}&pid=&href=&icon=layui-icon-login-wechat&weight=0
#WEB rule/update title=biaoti{new_rule_id}a&key=biaozhi{new_rule_id}x&pid=&href=&icon=layui-icon-login-wechat&type=1&weight=0&id={new_rule_id}
#WEB rule/update title=biaoti{new_rule_id}a&key=biaozhi{new_rule_id}x&pid=&href=&icon=layui-icon-login-wechat&type=1&weight=0&id={new_rule_id}&pid=0

#WEB admin/delete id={new_admin_id}
#WEB role/delete id={new_role_id}
#WEB rule/delete id={new_rule_id}

#WEB 
#WEB account/logout
#WEB index


#WEB account/login username=admin&password=123456&captcha=7268
#SETWEB _ _ {static}@runExtActions
#WEB index

#CALL {static}@runExtBusiness

#CALL {static}@endTest
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
        
        $database_driver = DuckAdminApp::_()->options['database_driver'];
        if($database_driver ==='mysql'){
            $sql = "truncate `'TABLE'`";
            $sql = str_replace("`'TABLE'`",$table,$sql);
            $data = DbManager::Db()->execute($sql);
        }else {
            $sql = "delete from `'TABLE'`";
            $sql = str_replace("`'TABLE'`",$table,$sql);
            $data = DbManager::Db()->execute($sql);
            
            $sql = "delete from sqlite_sequence where name = ?";
            $sql = str_replace("`'TABLE'`",$table,$sql);
            $data = DbManager::Db()->execute($sql);
        }
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
            $ret = \DuckPhp\Component\DbManager::Db()->fetchColumn($sql,\DuckAdmin\System\DuckAdminApp::_()->options['table_prefix'] .$table);
            $ret =(int)$ret+1;
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
    public function runExtActions()
    {
        \DuckAdmin\Controller\AdminAction::_()->urlForLogin();
        \DuckAdmin\Controller\AdminAction::_()->urlForLogout();
        \DuckAdmin\Controller\AdminAction::_()->urlForHome();
        
        try{
        \DuckAdmin\Controller\AdminAction::_()->service();
        }catch(\Throwable $ex){}
        try{
        \DuckAdmin\Controller\AdminAction::_()->login([]);
        }catch(\Throwable $ex){}
        try{
        \DuckAdmin\Controller\AdminAction::_()->isSuper();
        }catch(\Throwable $ex){}
    }
    public function runExtBusiness()
    {
        
        ///pid=1&name=jsm1&rules=2%2C3%2C4%2C6%2C8%2C9
        ///pid=2&name=jsm2&rules=8%2C9
        ///admin/insert roles=2&username=gly1&nickname=guanliyuan1&password=123456&email=&mobile=
        //DbManager::_()->options['database_log_sql_query']=true;
        $root_id =1;
        $input = ['pid'=>'1','name'=>'jsm1','rules'=>'2,3,4,6,8,9'];
        $new_role1 = \DuckAdmin\Business\RoleBusiness::_()->insertRole($root_id,$input);
        var_dump($new_role1);
        $input = ['pid'=>$new_role1,'name'=>'jsm2','rules'=>'8,9'];
        $new_role2 =\DuckAdmin\Business\RoleBusiness::_()->insertRole($root_id,$input);
        var_dump($new_role2);
        $admin_id =\DuckAdmin\Business\AdminBusiness ::_()->addAdmin($root_id,['roles'=>$new_role1,'username'=>'ua_'.$new_role1,'nickname'=>'na_'.$new_role1, 'password'=>'123456','email'=>'','mobile'=>'']);
        \DuckAdmin\Business\AccountBusiness::_()->canAccess($admin_id,\DuckAdmin\Controller\AdminController::class,'index');
        
        \DuckAdmin\Business\AdminBusiness ::_()->showAdmins($admin_id,[]);

        
        $child_admin_id = \DuckAdmin\Business\AdminBusiness ::_()->addAdmin($admin_id,['roles'=>$new_role2,'username'=>'ua_'.$new_role2,'nickname'=>'na_'.$new_role2, 'password'=>'123456','email'=>'','mobile'=>'']);
        
        
        var_dump($child_admin_id);
        
        
        \DuckAdmin\Business\RuleBusiness::_()->get($admin_id,[0,1]);
        \DuckAdmin\Business\RuleBusiness::_()->permission($admin_id);
        
        $flag = \DuckAdmin\Business\AdminBusiness ::_()->updateAdmin($admin_id,['id'=>$child_admin_id,'roles'=>$new_role2,'username'=>'u_a'.$new_role2,'nickname'=>'na_'.$new_role2, 'password'=>'123456','email'=>'','mobile'=>'']);
        
        \DuckAdmin\Business\RuleBusiness::_()->get($child_admin_id,[0,1]);
        
        //\DuckAdmin\Business\AdminBusiness ::_()->deleteAdmin($op_id,$child_admin_id);
        
        \DuckAdmin\Business\RoleBusiness ::_()->tree($admin_id,$new_role1);
        \DuckAdmin\Business\RoleBusiness ::_()->tree($admin_id,$new_role2);
        
        //\DuckAdmin\Business\RoleBusiness ::_()->updateRole(1,$new_role2);
        
        $input = ['id'=>'1','name'=>'超级管理员','rules'=>'1,2'];
        \DuckAdmin\Business\RoleBusiness ::_()->updateRole($root_id,$input);
        
        $input = ['pid'=>$new_role1,'name'=>'jsm2','rules'=>''];
        $input['id']=$new_role2;
        \DuckAdmin\Business\RoleBusiness ::_()->updateRole($root_id,$input);
        unset($input['pid']);
        \DuckAdmin\Business\RoleBusiness ::_()->updateRole($root_id,$input);
        \DuckAdmin\Business\RoleBusiness ::_()->tree($admin_id,$new_role2);
        
        \DuckAdmin\Business\RuleBusiness::_()->permission($child_admin_id);
        
        //改一下 title sym
        $table = \DuckAdmin\System\DuckAdminApp::_()->options['table_prefix'].'rules';
        $sql = "select id from $table where type =2 order by id desc limit 1";
        $id =DbManager::Db()->fetchColumn($sql);;
        $sql = "update $table set title ='--' where id = ?";
        $flag = DbManager::Db()->execute($sql,$id);
        
        \DuckAdmin\Business\RuleBusiness::_()->selectRules($root_id,[]);
        
        //删除后自动添加回来
        $str='DuckAdmin\Controller\AdminController@update';
        $sql = "delete from $table where `key`= ? limit 1";
        $flag = DbManager::Db()->execute($sql,$str);
        \DuckAdmin\Business\RuleBusiness::_()->selectRules($root_id,[]);
        
        $table = \DuckAdmin\System\DuckAdminApp::_()->options['table_prefix'].'roles';

        $sql = "update $table set rules =',,' where id = ?";
        $flag = DbManager::Db()->execute($sql,$new_role2);
        \DuckAdmin\Business\RuleBusiness::_()->permission($child_admin_id);
        $sql = "update $table set rules ='' where id = ?";
        $flag = DbManager::Db()->execute($sql,$new_role2);
        
        \DuckAdmin\Business\RuleBusiness::_()->permission($admin_id);

        
        // 清理现场
        \DuckAdmin\Business\AdminBusiness ::_()->deleteAdmin($admin_id,$child_admin_id);
        \DuckAdmin\Business\RoleBusiness ::_()->deleteRole($root_id,$new_role1); //$new_role2是$new_role1 的子role 连同删除
        // 我们增加两个 role ，然后 addadmin ，然后再处理 rule.
        \DuckAdmin\Business\AdminBusiness ::_()->deleteAdmin($root_id,$admin_id);
        $this->runTestTree();
        $this->runExtModel();
        
    }
    protected $tree_data =[];
    public function toArray()
    {
        return $this->tree_data;
    }
    public function runTestTree()
    {
        \DuckAdmin\Business\Tree::arrayValues([]);
        $this->tree_data=[];
        $tree =new \DuckAdmin\Business\Tree($this);
        $tree->getDescendant([4]);
        $tree->getTree([],\DuckAdmin\Business\Tree::EXCLUDE_ANCESTORS);
        $this->tree_data=[
            ['id'=>'1','pid'=>0,'name'=>'a'],
            ['id'=>'2','pid'=>1,'name'=>'b'],
            ['id'=>'3','pid'=>1,'name'=>'c'],
        ];
        $tree =new \DuckAdmin\Business\Tree($this);
        $tree->getDescendant([]);
        $tree->getTree([4],\DuckAdmin\Business\Tree::EXCLUDE_ANCESTORS);
        $tree->getTree([1],\DuckAdmin\Business\Tree::EXCLUDE_ANCESTORS);
        $tree->getTree([4],1111);

    }

    public function runExtModel()
    {
        ////[[[[
        $menus =  Helper::Config('menu',null,[]);
        \DuckAdmin\Model\RuleModel::_()->importMenu($menus);
        ////]]]]
    }
    public function runAllExtract()
    {
        // 这里是 System 的其他测试
    }
    //// start ////
    
}
