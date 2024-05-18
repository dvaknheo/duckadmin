<?php
namespace DuckAdmin\Test;

use DuckPhp\Foundation\SimpleSingletonTrait;

class Tester
{
    use SimpleSingletonTrait;
    
    public function test()
    {
        AdminModel::_()->foo();
    }
    public function runInConsole()
    {
        //$this->runAllBusiness();
        //$this->runAllModel();
        //$this->runAllExtract();
    }
    public function getTestList()
    {
        $list = <<<EOT
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
#WEB account/update
#WEB account/logout
#WEB account/logout  AJAX

#WEB account/dashboard
#WEB account/login username=admin&password=123456&captcha=7268
#WEB admin/index?diffname  OPTIONS

#WEB admin/index
#WEB admin/select
#WEB admin/insert
#WEB admin/update
#WEB admin/delete
#WEB admin/insert roles=1&username=admin{new_admin_id}&nickname=the_admin{new_admin_id}&password=123456&email=youxiang{new_admin_id}&mobile=shouji{new_admin_id}
#WEB admin/update roles=-1&username=admin{new_admin_id}&nickname=the_admin_new{new_admin_id}&password=&email=xyouxiang{new_admin_id}&mobile=xshouji{new_admin_id}&id={new_admin_id}
#WEB account/login username=admin{new_admin_id}&password=123456&captcha=7268
#WEB admin/insert
#WEB account/login username=admin&password=123456&captcha=7268

#WEB config/index
#WEB config/get
#WEB config/update


##WEB account/login username=admin&password=123456&captcha=7268
#WEB role/index
#WEB role/select
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

EOT;
        //*/

        $last_phase = \DuckAdmin\System\DuckAdminApp::Phase(\DuckAdmin\System\DuckAdminApp::class);
        $new_admin_id = $this->getNextInsertId('admins');
        $new_role_id = $this->getNextInsertId('roles');
        $new_rule_id = $this->getNextInsertId('rules');
        
        //$list ="#WEB index?foroptions=1  OPTIONS\n";
        //$list = "#WEB account/logout  AJAX\n";
        $args = [
            'new_admin_id'=>$new_admin_id,
            'new_role_id'=>$new_role_id,
            'new_rule_id'=>$new_rule_id,
        ];
        $list = $this->replace_string($list,$args);
        $prefix = \DuckAdmin\System\DuckAdminApp::_()->options['controller_url_prefix'];
        $list = str_replace('#WEB ','#WEB '.$prefix,$list);
        
        \DuckAdmin\System\DuckAdminApp::Phase($last_phase);
        return $list;
    }
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
    public function runAllController()
    {
        ////[[[[
        \DuckAdmin\Controller\AccountController::_()->index();
        \DuckAdmin\Controller\AccountController::_()->login();
        \DuckAdmin\Controller\AccountController::_()->logout();
        \DuckAdmin\Controller\AccountController::_()->info();
        \DuckAdmin\Controller\AccountController::_()->update();
        \DuckAdmin\Controller\AccountController::_()->password();
        \DuckAdmin\Controller\AccountController::_()->captcha();

        \DuckAdmin\Controller\AdminController::_()->index();
        \DuckAdmin\Controller\AdminController::_()->select();
        \DuckAdmin\Controller\AdminController::_()->insert();
        \DuckAdmin\Controller\AdminController::_()->update();
        \DuckAdmin\Controller\AdminController::_()->delete();

        \DuckAdmin\Controller\ConfigController::_()->index();
        \DuckAdmin\Controller\ConfigController::_()->get();
        \DuckAdmin\Controller\ConfigController::_()->update();

        \DuckAdmin\Controller\MainController::_()->index();
        \DuckAdmin\Controller\MainController::_()->dashboard();

        \DuckAdmin\Controller\RoleController::_()->index();
        \DuckAdmin\Controller\RoleController::_()->select();
        \DuckAdmin\Controller\RoleController::_()->insert();
        \DuckAdmin\Controller\RoleController::_()->update();
        \DuckAdmin\Controller\RoleController::_()->delete();
        \DuckAdmin\Controller\RoleController::_()->rules();

        \DuckAdmin\Controller\RuleController::_()->index();
        \DuckAdmin\Controller\RuleController::_()->select();
        \DuckAdmin\Controller\RuleController::_()->get();
        \DuckAdmin\Controller\RuleController::_()->permission();
        \DuckAdmin\Controller\RuleController::_()->insert();
        \DuckAdmin\Controller\RuleController::_()->update();
        \DuckAdmin\Controller\RuleController::_()->delete();
        ////]]]]
    }
    public function runAllBusiness()
    {
        ////[[[[
        \DuckAdmin\Business\AccountBusiness::_()->getAccountInfo($admin);
        \DuckAdmin\Business\AccountBusiness::_()->login($username,$password);
        \DuckAdmin\Business\AccountBusiness::_()->canAccess($admin_id, $controller,  $action);
        \DuckAdmin\Business\AccountBusiness::_()->update($admin_id, $data);
        \DuckAdmin\Business\AccountBusiness::_()->changePassword($admin_id, $old_password, $password, $password_cofirm);

        \DuckAdmin\Business\AdminBusiness::_()->showAdmins($op_id,$input);
        \DuckAdmin\Business\AdminBusiness::_()->addAdmin($op_id, $input);
        \DuckAdmin\Business\AdminBusiness::_()->updateAdmin($op_id,$input);
        \DuckAdmin\Business\AdminBusiness::_()->deleteAdmin($op_id, $ids);

        \DuckAdmin\Business\ConfigBusiness::_()->getDefaultConfig();
        \DuckAdmin\Business\ConfigBusiness::_()->updateConfig($post);

        \DuckAdmin\Business\InstallBusiness::_()->install($username,$password,$password_confirm);

        \DuckAdmin\Business\RoleBusiness::_()->selectRoles($op_id,$id,$input);
        \DuckAdmin\Business\RoleBusiness::_()->insertRole($op_id, $data);
        \DuckAdmin\Business\RoleBusiness::_()->updateRole($op_id, $input);
        \DuckAdmin\Business\RoleBusiness::_()->deleteRole($op_id, $ids);
        \DuckAdmin\Business\RoleBusiness::_()->tree($op_id, $role_id);

        \DuckAdmin\Business\RuleBusiness::_()->get($roles,$types);
        \DuckAdmin\Business\RuleBusiness::_()->selectRules($op_id, $input);
        \DuckAdmin\Business\RuleBusiness::_()->permission($roles);
        \DuckAdmin\Business\RuleBusiness::_()->insertRule($op_id, $input);
        \DuckAdmin\Business\RuleBusiness::_()->updateRule($op_id, $input);
        \DuckAdmin\Business\RuleBusiness::_()->deleteRule($op_id, $ids);
        \DuckAdmin\Business\RuleBusiness::_()->controllerToUrlPath($controller_class);
        ////]]]]
    }
    public function runAllModel()
    {
        ////[[[[
        \DuckAdmin\Model\AdminModel::_()->passwordVerify($password, $admin_password);
        \DuckAdmin\Model\AdminModel::_()->inputFilter($data);
        \DuckAdmin\Model\AdminModel::_()->selectInput($data);
        \DuckAdmin\Model\AdminModel::_()->doSelect($where, $field, $order= 'desc' ,$page=1,$page_size=10);
        \DuckAdmin\Model\AdminModel::_()->getAdminByName($username);
        \DuckAdmin\Model\AdminModel::_()->getAdminById($admin_id);
        \DuckAdmin\Model\AdminModel::_()->hasAdmins();
        \DuckAdmin\Model\AdminModel::_()->deleteByIds($ids);
        \DuckAdmin\Model\AdminModel::_()->addFirstAdmin($username,$password);
        \DuckAdmin\Model\AdminModel::_()->addAdmin( $data);
        \DuckAdmin\Model\AdminModel::_()->updateLoginAt($admin_id);
        \DuckAdmin\Model\AdminModel::_()->updateAdmin($admin_id, $data);
        \DuckAdmin\Model\AdminModel::_()->checkPasswordByAdmin($admin, $password);
        \DuckAdmin\Model\AdminModel::_()->checkPassword($admin_id, $password);
        \DuckAdmin\Model\AdminModel::_()->changePassword($admin_id,$password);

        \DuckAdmin\Model\AdminRoleModel::_()->getRoles($admin_id);
        \DuckAdmin\Model\AdminRoleModel::_()->addFirstRole($admin_id);
        \DuckAdmin\Model\AdminRoleModel::_()->getAdminRoles($admin_ids);
        \DuckAdmin\Model\AdminRoleModel::_()->renew($admin_id,$role_ids);
        \DuckAdmin\Model\AdminRoleModel::_()->deleteByAdminIds($ids);
        \DuckAdmin\Model\AdminRoleModel::_()->deleteByAdminId($admin_id,$delete_ids);
        \DuckAdmin\Model\AdminRoleModel::_()->updateAdminRole($admin_id, $exist_role_ids, $role_ids);
        \DuckAdmin\Model\AdminRoleModel::_()->rolesByAdmin($admin_id);

        \DuckAdmin\Model\OptionModel::_()->getSystemConfig();
        \DuckAdmin\Model\OptionModel::_()->setSystemConfig($value);

        \DuckAdmin\Model\RoleModel::_()->selectInput($data);
        \DuckAdmin\Model\RoleModel::_()->doSelect( $where,  $field = null,  $order= 'desc' ,$page=1,$page_size=10);
        \DuckAdmin\Model\RoleModel::_()->inputFilter($data);
        \DuckAdmin\Model\RoleModel::_()->getRules($roles);
        \DuckAdmin\Model\RoleModel::_()->hasSuperAdmin($roles);
        \DuckAdmin\Model\RoleModel::_()->getAllId();
        \DuckAdmin\Model\RoleModel::_()->getAll();
        \DuckAdmin\Model\RoleModel::_()->getById($id);
        \DuckAdmin\Model\RoleModel::_()->getRulesByRoleId($role_id);
        \DuckAdmin\Model\RoleModel::_()->getAllIdPid();
        \DuckAdmin\Model\RoleModel::_()->deleteByIds($ids);
        \DuckAdmin\Model\RoleModel::_()->addRole($data);
        \DuckAdmin\Model\RoleModel::_()->updateRole($id, $data);
        \DuckAdmin\Model\RoleModel::_()->updateRoleMore($descendant_role_ids,$rule_ids);
        \DuckAdmin\Model\RoleModel::_()->addFirstRole();

        \DuckAdmin\Model\RuleModel::_()->selectInput($data);
        \DuckAdmin\Model\RuleModel::_()->doSelect( $where,  $field = null,  $order= 'desc' ,$page=1,$page_size=10);
        \DuckAdmin\Model\RuleModel::_()->inputFilter( $data);
        \DuckAdmin\Model\RuleModel::_()->isSuper($rules);
        \DuckAdmin\Model\RuleModel::_()->allRules();
        \DuckAdmin\Model\RuleModel::_()->checkWildRules($rule_ids,$controller,$action);
        \DuckAdmin\Model\RuleModel::_()->checkRules($rule_ids,$controller,$action);
        \DuckAdmin\Model\RuleModel::_()->allRulesForTree();
        \DuckAdmin\Model\RuleModel::_()->findById($id);
        \DuckAdmin\Model\RuleModel::_()->findByKey($key);
        \DuckAdmin\Model\RuleModel::_()->dropWithChildren($ids);
        \DuckAdmin\Model\RuleModel::_()->updateTitleByKey($name,$title);
        \DuckAdmin\Model\RuleModel::_()->updateRule($id, $data);
        \DuckAdmin\Model\RuleModel::_()->addMenu($key,$menu);
        \DuckAdmin\Model\RuleModel::_()->deleteAll($key);
        \DuckAdmin\Model\RuleModel::_()->importMenu( $menu_tree);
        \DuckAdmin\Model\RuleModel::_()->getKeysByIds($rules);
        \DuckAdmin\Model\RuleModel::_()->checkRulesExist($rule_ids);
        \DuckAdmin\Model\RuleModel::_()->getAllByKey();
        ////]]]]
    }
    public function runAllExtract()
    {
        //
    }
    //// start ////
    
}
