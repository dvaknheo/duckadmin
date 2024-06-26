<?php
namespace DuckAdmin\Business;

use DuckAdmin\Model\AdminModel;
use DuckAdmin\Model\AdminRoleModel;
use DuckAdmin\Model\RoleModel;
use DuckAdmin\Model\RuleModel;

/**
 * 个人资料业务
 */
class AccountBusiness extends Base
{
    /////////////
    public function getAccountInfo($admin_id)
    {
        $admin = AdminModel::_()->getAdminById($admin_id);
        
        $op_role_ids = AdminRoleModel::_()->rolesByAdminId($admin_id);
        $is_super = RoleModel::_()->hasSuper($op_role_ids);
        
        $info = [
            'id' => $admin['id'],
            'username' => $admin['username'],
            'nickname' => $admin['nickname'],
            'email' => $admin['email'],
            'mobile' => $admin['mobile'],
            'isSupperAdmin' =>$is_super,
        ];
        
        return $info;
    }
    public function getDashBoardInfo($admin_id)
    {
        // 这里要显示的是现在是谁
        return ['time_stamp'=>DATE(DATE_ATOM)];
    }
    public function login($username,$password)
    {
        Helper::BusinessThrowOn(!$username, '用户名不能为空',1);        
        $admin = AdminModel::_()->getAdminByName($username);
        Helper::BusinessThrowOn(!$admin,'账户不存在或密码错误');
        $flag = AdminModel::_()->checkPasswordByAdmin($admin, $password);
        Helper::BusinessThrowOn(!$flag,'账户不存在或密码错误');
        Helper::BusinessThrowOn($admin['status'] != 0, '当前账户暂时无法登录',1);
        
        // change roles
        //////////////////////////////////////////
        unset($admin['password']);
        AdminModel::_()->updateLoginAt($admin['id']);
        
        //Helper::FireEvent([static::class,__LOGIN__], $admin);
        return $admin;
    }
    
    /**
     * 判断是否有权限
     * @param string $controller
     * @param string $action
     * @param int $code
     * @param string $msg
     * @return bool
     * @throws \ReflectionException|BusinessException
     */
    public function canAccess($admin_id, string $controller, string $action): bool
    {
        // 根据反射获取控制器鉴权信息。 如果是 admincontroller
        try{
            $class = new \ReflectionClass($controller);
            $properties = $class->getDefaultProperties();
            $noNeedLogin = $properties['noNeedLogin'] ?? [];
            $noNeedAuth = $properties['noNeedAuth'] ?? [];
        }catch(\ReflectionException $ex){
            Helper::BusinessThrowOn(true, '访问错误', 302);
        }
        
        // 不需要登录
        if (in_array($action, $noNeedLogin)) {
            return true;
        }
        Helper::BusinessThrowOn(!$admin_id, $msg = '请登录', 401);
        
        // 不需要鉴权
        if (in_array($action, $noNeedAuth)) {
            return true;
        }        
        ////]]]]
        
        ////[[[[
        
        $admin = AdminModel::_()->getAdminById($admin_id); 
        Helper::BusinessThrowOn(!$admin, $msg = '登录已经过期', 302);
        Helper::BusinessThrowOn($admin['status'] != 0, $msg = '账户被禁用', 401);
        
        $roles = AdminRoleModel::_()->rolesByAdminId($admin_id);
        $rule_ids = RoleModel::_()->getRules($roles);
        $rule = RuleModel::_()->checkRules($rule_ids,$controller,$action);
        if (in_array('*', $rule_ids)){
            return true;
        }
        
        Helper::BusinessThrowOn(empty($rule), '你没有权限', 403);
        
        return true;
    }
    public function update($admin_id, $data)
    {
        Helper::BusinessThrowOn(!$data, '没数据',1);
        AdminModel::_()->updateAdmin($admin_id,$data);
        $admin = AdminModel::_()->getAdminById($admin_id);
        unset($admin['password']);
        return $admin;
    }
    public function changePassword($admin_id, $old_password, $password, $password_cofirm)
    {
        Helper::BusinessThrowOn(!$password, '密码不能为空',1);
        Helper::BusinessThrowOn($password !== $password_cofirm, '两次密码输入不一致',2);
        
        $flag = AdminModel::_()->checkPassword($admin_id, $old_password);
        Helper::BusinessThrowOn(!$flag, '原始密码不正确', 3);
        AdminModel::_()->changePassword($admin_id, $password);
    }
}