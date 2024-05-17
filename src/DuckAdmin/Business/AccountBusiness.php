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
    public function getAdmin($admin_id)
    {
        $admin = AdminModel::_()->getAdminById($admin_id); 
        
        if (!$admin || $admin['status'] != 0) {
            return null;
        }
        $admin['roles'] = AdminRoleModel::_()->getRoles($admin_id);
        return $admin;
    }
    /////////////
    public function getAccountInfo($admin)
    {
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
        return [];
    }
    public function login($username,$password)
    {
        Helper::BusinessThrowOn(!$username, '用户名不能为空',1);
        
        //$this->checkLoginLimit($username);
        $admin = AdminModel::_()->getAdminByName($username);
        Helper::BusinessThrowOn(!$admin,'账户不存在或密码错误');
        $flag = AdminModel::_()->checkPasswordByAdmin($admin, $password);
        Helper::BusinessThrowOn(!$flag,'账户不存在或密码错误');
        Helper::BusinessThrowOn($admin['status'] != 0, '当前账户暂时无法登录',1);
        
        // change roles
        //////////////////////////////////////////
        unset($admin['password']);
        
        $admin['roles'] = AdminRoleModel::_()->getRoles($admin['id']);

        AdminModel::_()->updateLoginAt($admin['id']);
        
        //$this->removeLoginLimit($username);
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
        
        ////[[[[ 这段是控制器内的。
        // 无控制器信息说明是函数调用，函数不属于任何控制器，鉴权操作应该在函数内部完成。
        if (!$controller) {
            return true;
        }
        // 获取控制器鉴权信息
        $class = new \ReflectionClass($controller);
        $properties = $class->getDefaultProperties();
        $noNeedLogin = $properties['noNeedLogin'] ?? [];
        $noNeedAuth = $properties['noNeedAuth'] ?? [];

        // 不需要登录
        if (in_array($action, $noNeedLogin)) {
            return true;
        }
        // 不需要鉴权
        if (in_array($action, $noNeedAuth)) {
            return true;
        }        
        ////]]]]
        
        ////[[[[
        
        $admin = AdminModel::_()->getAdminById($admin_id); 
        Helper::BusinessThrowOn(!$admin, $msg = '请登录', 401);
        Helper::BusinessThrowOn($admin['status'] != 0, $msg = '账户被禁用', 401);
        
        $roles = AdminRoleModel::_()->getRoles($admin_id);
        Helper::BusinessThrowOn(!$roles,  '无权限', 2); //当前管理员无角色
        ////]]]]

        // 角色没有规则
        $rule_ids = RoleModel::_()->getRules($roles);
        Helper::BusinessThrowOn(!$rule_ids,  '无权限', 2);
        // 超级管理员
        if (in_array('*', $rule_ids)){
            return true;
        }

        // 如果action为index，规则里有任意一个以$controller开头的权限即可
        // 这两段长得一样？
        if (strtolower($action) === 'index') {
            $rule = RuleModel::_()->checkWildRules($rule_ids,$controller,$action);
            Helper::BusinessThrowOn(!$rule, '无权限', 2);
            return true;
        }else{
            // 查询是否有当前控制器的规则
            $rule = RuleModel::_()->checkRules($rule_ids,$controller,$action);
            Helper::BusinessThrowOn(!$rule, '无权限', 2);
            return true;
        }
    }
    public function update($admin_id, $data)
    {
        if(!$data){
            return [];
        }
        AdminModel::_()->updateAdmin($admin_id,$data);
        
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