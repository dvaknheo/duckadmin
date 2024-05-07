<?php
namespace DuckAdmin\Business;

use DuckPhp\Component\DbManager; //TODO

use DuckAdmin\Model\AdminModel;
use DuckAdmin\Model\AdminRoleModel;
use DuckAdmin\Model\RoleModel;
use DuckAdmin\Model\RuleModel;
use DuckAdmin\System\DuckAdminApp;


/**
 * 这个安装是 web 的安装
 */
class InstallBusiness extends Base
{
    public function isInstalled()
    {
        //TODO 放到 Helper 里
        return DuckAdminApp::_()->isInstalled();
    }
    ///////////////////////////

    public function step1($post)
    {
        return true;
    }
    ////////////
    /**
     * 设置管理员

     */
    public function step2($username,$password,$password_confirm)
    {
        Helper::BusinessThrowOn($flag, '后台已经安装完毕，无法通过此页面创建管理员',1);
        Helper::BusinessThrowOn($password !== $password_confirm, '两次密码不一致',1);
        RoleModel::_()->addFirstRole();
        $menus =  Helper::Config('menu',null,[]);
        RuleModel::_()->importMenu($menus);
        $flag = AdminModel::_()->hasAdmins();
        
        try{
            $admin_id = AdminModel::_()->addFirstAdmin($username, $password);
            AdminRoleModel::_()->addFirstRole($admin_id);
            return true;
        }catch(\Throwable $ex){
            var_dump($ex);
            return false;
        }
    }


}