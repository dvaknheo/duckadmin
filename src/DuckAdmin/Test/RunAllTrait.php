<?php
namespace DuckAdmin\Test;
use DuckAdmin\Model\AdminModel;
//@codeCoverageIgnoreStart
trait RunAllTrait
{
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
    public function getAllRouteToRun()
    {
        $list = <<<EOT
#COMMAND FUNCTION_METHOD
#POST username=admin&password=123456&captcha=7268

#WEB account/login
#WEB account/index
#WEB account/info
#WEB account/password
#WEB account/update
###WEB account/captcha

#WEB admin/index
#WEB admin/select
#WEB admin/insert
#WEB admin/update
#WEB admin/delete

#WEB config/index
#WEB config/get
#WEB config/update

#WEB 
#WEB dashboard

#WEB role/index
#WEB role/select
#WEB role/insert
#WEB role/update
###WEB role/delete
#WEB role/rules

#WEB rule/index
#WEB rule/select
#WEB rule/get
#WEB rule/permission
#WEB rule/insert
#WEB rule/update
###WEB rule/delete

#WEB account/logout
#WEB 

EOT;
        return $list;
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
        \DuckAdmin\Business\AccountBusiness::_()->getAdmin($admin_id);
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

//@codeCoverageIgnoreEnd