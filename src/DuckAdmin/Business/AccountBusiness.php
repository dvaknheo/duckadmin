<?php
namespace DuckAdmin\Business;

use DuckAdmin\Business\Base;
use DuckAdmin\Business\Base as Helper;

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
            'avatar' => $admin['avatar'],
            'email' => $admin['email'],
            'mobile' => $admin['mobile'],
            'isSupperAdmin' =>CommonService::_()->isSupperAdmin($admin['id']), //TODO
        ];
        
        return $info;
    }

    public function login($username,$password)
    {
        BusinessException::ThrowOn(!$username, '用户名不能为空',1);
        
        //$this->checkLoginLimit($username);
        $admin = AdminModel::_()->getAdminByName($username);
        BusinessException::ThrowOn(!$admin,'账户不存在或密码错误');
        $flag = AdminModel::_()->checkPasswordByAdmin($admin, $password);
        BusinessException::ThrowOn(!$flag,'账户不存在或密码错误');
        BusinessException::ThrowOn($admin['status'] != 0, '当前账户暂时无法登录',1);
        //////////////////////////////////////////
        unset($admin['password']);
        AdminModel::_()->updateLoginAt($admin['id']);
        
        //$this->removeLoginLimit($username);
        static::FireEvent([static::class,__METHOD__], $admin);

        return $admin;
    }
    /**
     * 检查登录频率限制
     * @param $username
     * @return void
     * @throws BusinessException
     */
    protected function checkLoginLimit($username)
    {
        //TODO
        $limit_log_path = runtime_path() . '/login';
        if (!is_dir($limit_log_path)) {
            mkdir($limit_log_path, 0777, true);
        }
        $limit_file = $limit_log_path . '/' . md5($username) . '.limit';
        $time = date('YmdH') . ceil(date('i')/5);
        $limit_info = [];
        if (is_file($limit_file)) {
            $json_str = file_get_contents($limit_file);
            $limit_info = json_decode($json_str, true);
        }

        if (!$limit_info || $limit_info['time'] != $time) {
            $limit_info = [
                'username' => $username,
                'count' => 0,
                'time' => $time
            ];
        }
        $limit_info['count']++;
        file_put_contents($limit_file, json_encode($limit_info));
        if ($limit_info['count'] >= 5) {
            throw new BusinessException('登录失败次数过多，请5分钟后再试');
        }
    }

    /**
     * 解除登录频率限制
     * @param $username
     * @return void
     */
    protected function removeLoginLimit($username)
    {
        $limit_log_path = runtime_path() . '/login'; //runtime_path() 
        $limit_file = $limit_log_path . '/' . md5($username) . '.limit';
        if (is_file($limit_file)) {
            unlink($limit_file);
        }
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
    public function canAccess($admin, string $controller, string $action, int &$code = 0, string &$msg = ''): bool
    {
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
        
        // 无控制器信息说明是函数调用，函数不属于任何控制器，鉴权操作应该在函数内部完成。
        if (!$controller) {
            return true;
        }
        try{

            BusinessException::ThrowOn(!$admin, $msg = '请登录', 401);
            // 当前管理员无角色
            $roles = $admin['roles'];
            BusinessException::ThrowOn(!$roles,  '无权限', 2);

            // 角色没有规则
            $rule_ids = RoleModel::_()->getRules($roles);
            BusinessException::ThrowOn(!$rule_ids,  '无权限', 2);
            
            // 超级管理员
            if (in_array('*', $rule_ids)){
                return true;
            }

            // 如果action为index，规则里有任意一个以$controller开头的权限即可
            if (strtolower($action) === 'index') {
                $rule = RuleModel::_()->checkWildRules($rule_ids,$controller,$action);
                BusinessException::ThrowOn(!$rule, '无权限', 2);
                return true;
            }else{
                // 查询是否有当前控制器的规则
                $rule = RuleModel::_()->checkRules($rule_ids,$controller,$action);
                BusinessException::ThrowOn(!$rule, '无权限', 2);
                return true;
            }
        }catch(\Exception $ex){
            $code = $ex->getCode();
            $msg = $ex->getMessage();
            return false;
        }
        return true;
    }
    public function update($admin_id, $data)
    {
        $update_data = AdminModel::_()->updateAdmin($admin_id,$data);
        
        foreach ($update_data as $key => $value) {
            $admin[$key] = $value;
        }
        return $admin;
    }
    public function changePassword($admin_id, $old_password, $password, $password_cofirm)
    {
        BusinessException::ThrowOn(!$password, '密码不能为空',2);
        BusinessException::ThrowOn($password !== $password_cofirm, '两次密码输入不一致',3);
        
        $flag = AdminModel::_()->checkPasword($admin_id, $old_password);
        BusinessException::ThrowOn(!$flag, '原始密码不正确', 1);
        AdminModel::_()->updateAdminPassword($admin_id, $password);
    }
}