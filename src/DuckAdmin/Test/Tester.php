<?php
namespace DuckAdmin\Test;

use DuckPhp\Foundation\SimpleSingletonTrait;
use DuckAdmin\System\DuckAdminApp;
use Demo\Test\MyCoverageBridge;
use \DuckPhp\Component\DbManager;
class Tester
{
    use SimpleSingletonTrait;
    public function runInConsole()
    {
        //$this->runExtBusiness();
        //$this->runExtModel();
        //$this->runExtExtract();
    }
    public function KickTestDirectory()
    {
        $last_phase = DuckAdminApp::Phase(DuckAdminApp::class);
        static::_()->_kickTestDirectory();
        DuckAdminApp::Phase($last_phase);
    }
    public function _kickTestDirectory()
    {
        // 这段无法生效是因为在web  call 不影响环境
        $path = DuckAdminApp::_()->options['path'];
        $filter = MyCoverageBridge::_()->getCoverage()->filter();
        $filter->removeDirectoryFromWhitelist($path.'Test');
        $filter->removeDirectoryFromWhitelist($path.'View');
    }
    public function getTestList()
    {
        $list = <<<EOT
#CALL {static}::KickTestDirectory
#WEB account/dashboard  AJAX
#WEB account/login
#WEB account/login username=admin&password=123456&captcha=7268
#WEB account/info
#WEB account/dashboard
#WEB account/index
#WEB account/captcha
#WEB account/password
#WEB account/password old_password=123456&password=654321&password_confirm=654321
#WEB account/password old_password=654321&password=123456&password_confirm=123456
#WEB account/update nickname=%E8%B6%85%E7%BA%A7%E7%AE%A1%E7%90%86%E5%91%98&email=112233a&mobile=22244b
#WEB account/logout
#WEB account/logout  AJAX

#WEB account/dashboard
#WEB account/login username=admin&password=123456&captcha=7268
#WEB admin/index?diffname  OPTIONS

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
##TODO 我们创建个 role， rule 以完成其他 business 测试
#WEB admin/delete id={new_admin_id}
#WEB role/delete id={new_role_id}
#WEB rule/delete id={new_rule_id}

#WEB 
#WEB account/logout
#WEB index


EOT;
        $last_phase = \DuckAdmin\System\DuckAdminApp::Phase(\DuckAdmin\System\DuckAdminApp::class);
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
        
        $prefix = \DuckAdmin\System\DuckAdminApp::_()->options['controller_url_prefix'];
        $list = str_replace('#WEB ','#WEB '.$prefix,$list);
        \DuckAdmin\System\DuckAdminApp::Phase($last_phase);
        return $list;
    }
    public function BeginConfigBusiness()
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
    public function EndConfigBusiness()
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
        $database_driver = \DuckAdmin\System\DuckAdminApp::_()->options['database_driver'];
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

        \DuckAdmin\Business\AccountBusiness::_()->canAccess($admin_id, $controller,  $action);
        \DuckAdmin\Business\AdminBusiness::_()->updateAdmin($op_id,$input);
        \DuckAdmin\Business\AdminBusiness::_()->deleteAdmin($op_id, $ids);

        \DuckAdmin\Business\ConfigBusiness::_()->getDefaultConfig();
        \DuckAdmin\Business\ConfigBusiness::_()->updateConfig($post);

        \DuckAdmin\Business\InstallBusiness::_()->install($username,$password,$password_confirm);
        
        \DuckAdmin\Business\RoleBusiness::_()->updateRole($op_id, $input);
        \DuckAdmin\Business\RoleBusiness::_()->deleteRole($op_id, $ids);
        \DuckAdmin\Business\RoleBusiness::_()->tree($op_id, $role_id);

        \DuckAdmin\Business\RuleBusiness::_()->get($roles,$types);
        \DuckAdmin\Business\RuleBusiness::_()->permission($roles);
        \DuckAdmin\Business\RuleBusiness::_()->insertRule($op_id, $input);
        \DuckAdmin\Business\RuleBusiness::_()->updateRule($op_id, $input);
        \DuckAdmin\Business\RuleBusiness::_()->controllerToUrlPath($controller_class);
        ////]]]]
        // CommonService, Tree
    }
    public function runExtModel()
    {
        ////[[[[

        \DuckAdmin\Model\AdminModel::_()->hasAdmins();
        \DuckAdmin\Model\AdminModel::_()->addFirstAdmin($username,$password);
        \DuckAdmin\Model\AdminRoleModel::_()->addFirstRole($admin_id);

        \DuckAdmin\Model\OptionModel::_()->setSystemConfig($value);

        \DuckAdmin\Model\RoleModel::_()->updateRoleMore($descendant_role_ids,$rule_ids);
        \DuckAdmin\Model\RoleModel::_()->addFirstRole();


        \DuckAdmin\Model\RuleModel::_()->checkRules($rule_ids,$controller,$action);
        \DuckAdmin\Model\RuleModel::_()->allRulesForTree();
        \DuckAdmin\Model\RuleModel::_()->updateTitleByKey($name,$title);
        \DuckAdmin\Model\RuleModel::_()->deleteAll($key);
        \DuckAdmin\Model\RuleModel::_()->importMenu( $menu_tree);
        \DuckAdmin\Model\RuleModel::_()->getKeysByIds($rules);
        \DuckAdmin\Model\RuleModel::_()->getAllByKey();
        ////]]]]
    }
    public function runAllExtract()
    {
        // 这里是 System 的其他测试
    }
    //// start ////
    
}
