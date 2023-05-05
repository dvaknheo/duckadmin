<?php
namespace DuckAdmin\Business;

/**
 * 个人资料业务
 */
class AccountBusiness extends BaseBusiness 
{
	public function getAccountInfo()
	{
		$data = json_decode(file_get_contents(__DIR__.'/data/account.json'),true);
		return $data;
        $info = [
            'id' => $admin['id'],
            'username' => $admin['username'],
            'nickname' => $admin['nickname'],
            'avatar' => $admin['avatar'],
            'email' => $admin['email'],
            'mobile' => $admin['mobile'],

        ];
		return $info;
		

	}
    public static function isSupperAdmin(int $admin_id = 0): bool
    {
        if (!$admin_id) {
            if (!$roles = admin('roles')) {
                return false;
            }
        } else {
            $roles = AdminRole::where('admin_id', $admin_id)->pluck('role_id');
        }
        $rules = Role::whereIn('id', $roles)->pluck('rules');
        return $rules && in_array('*', $rules->toArray());
    }
	
}