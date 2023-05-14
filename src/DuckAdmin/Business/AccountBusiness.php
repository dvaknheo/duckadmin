<?php
namespace DuckAdmin\Business;
use DuckAdmin\Model\AdminRoleModel;
use DuckAdmin\Model\RoleModel;

/**
 * 个人资料业务
 */
class AccountBusiness extends BaseBusiness 
{
	public function getAdmin($admin_id)
	{
		$admin = AdminModel::G()->find($admin_id); 
		if (!$admin || $admin['status'] != 0) {
			return null;
		}
		$admin['roles'] = AdminRoleModel::G()->getRoles($admin_id);
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
            'isSupperAdmin' =>$this->isSupperAdmin($admin['id']),
        ];
		
		return $info;
	}
    public function isSupperAdmin(int $admin_id = 0): bool
    {
		static::ThrowOn($admin_id==0,'参数错误，请指定管理员');
		$roles = AdminRoleModel::G()->getRoles($admin_id);
        $rules = RoleModel::G()->getRules($roles);
        return $rules && in_array('*', $rules);
    }
	public function login($username,$password)
	{
        static::ThrowOn(!$username, '用户名不能为空',1);
		
        //$this->checkLoginLimit($username);
        $admin = AdminModel::G()->getUserByName($username);
		static::ThrowOn(!$admin,'账户不存在或密码错误');
		$flag = AdminModel::G()->passwordVerify($password, $admin['password']);
		static::ThrowOn(!$flag,'账户不存在或密码错误');
		static::ThrowOn($admin['status'] != 0, '当前账户暂时无法登录',1);
		//////////////////////////////////////////
		unset($admin['password']);
		AdminModel::G()->updateLoginAt($admin['id']);
        
		
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
}