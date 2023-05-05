<?php
namespace DuckAdmin\Business;

/**
 * 个人资料业务
 */
class AccountBusiness extends BaseBusiness 
{
	public function getAccountInfo($admin)
	{
        $info = [
            'id' => $admin['id'],
            'username' => $admin['username'],
            'nickname' => $admin['nickname'],
            'avatar' => $admin['avatar'],
            'email' => $admin['email'],
            'mobile' => $admin['mobile'],
            'isSupperAdmin' => statistatic::isSupperAdmin($admin['id']),
        ];
		
		return $info;
	}
    public function isSupperAdmin(int $admin_id = 0): bool
    {
		static::ThrowOn($admin_id==0,'参数错误，请指定管理员');
		$roles = AdminRoleModel::G()->getRoles($admin_id);
        $rules = RoleModel::G()->getRules($roles);
        return $rules && in_array('*', $rules->toArray());
    }
	public function login($username,$password,$captcha)
	{
		////[[[[ business
        static::ThrowOn(!$username, '用户名不能为空',1);
		
		// 不直接用 model 而是用business //这一片都是 business
        //$this->checkLoginLimit($username);
		
        $admin = AdminModel::G()->getUserByName($username);
        if (!$admin || !AdminModel::G()->passwordVerify($password, $admin->password)) {
            static::ThrowOn(true,'账户不存在或密码错误');
        }
		static::ThrowOn($admin->status != 0, '当前账户暂时无法登录',1);
		//////////////////////////////////////////
		
		AdminModel::G()->updateLoginAt($admin);
        
		
        //$this->removeLoginLimit($username);
		
		////]]]]
		
        $admin = $admin->toArray();
        unset($admin['password']);
		
        //statistatic::FireEvent([statistatic::class,__METHOD__], $admin);

		return $admin;
	}
    protected function removeLoginLimit($username)
    {
		//
        $limit_log_path = runtime_path() . '/login';
        $limit_file = $limit_log_path . '/' . md5($username) . '.limit';
        if (is_file($limit_file)) {
            unlink($limit_file);
        }
    }
}