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
        
        $info = [
            'id' => $admin['id'],
            'username' => $admin['username'],
            'nickname' => $admin['nickname'],
            'email' => $admin['email'],
            'mobile' => $admin['mobile'],
            'isSupperAdmin' =>CommonService::_()->isSupperAdmin($admin['id']?$admin['id']:0), //TODO
        ];
        
        return $info;
    }
    public function getDashBoardInfo($admin_id)
    {
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
        //try{
            $class = new \ReflectionClass($controller);
            $properties = $class->getDefaultProperties();
            $noNeedLogin = $properties['noNeedLogin'] ?? [];
            $noNeedAuth = $properties['noNeedAuth'] ?? [];
        //}catch(\ReflectionException){
            //Helper::BusinessThrowOn(true, '获取控制器失败', 403);
        //}
        
        // 不需要登录
        if (in_array($action, $noNeedLogin)) {
            return true;
        }
        Helper::BusinessThrowOn(!$admin_id, $msg = '请登录1', 401);
        
        // 不需要鉴权
        if (in_array($action, $noNeedAuth)) {
            return true;
        }        
        ////]]]]
        
        ////[[[[
        
        $admin = AdminModel::_()->getAdminById($admin_id); 
        Helper::BusinessThrowOn(!$admin, $msg = '请登录2', 401);
        Helper::BusinessThrowOn($admin['status'] != 0, $msg = '账户被禁用', 401);
        
        $roles = AdminRoleModel::_()->getRoles($admin_id);
        $rule_ids = RoleModel::_()->getRules($roles);
        
        $rule = RuleModel::_()->checkRules($rule_ids,$controller,$action);
        //Helper::BusinessThrowOn(!$roles,  '当前管理员无角色', 403); //当前管理员无角色
        //Helper::BusinessThrowOn(!$rule_ids,  '角色没有菜单', 403);
        // 我们搞一下，如果 rule 里没有当前 , 如果是超级管理员，那么我们直接加，并且提醒跳转
        // 如果 不是管理员，那当然就没有了，提示错误为： 没加入权限
        Helper::BusinessThrowOn(!$rule, '没当前访问权限', 403);
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