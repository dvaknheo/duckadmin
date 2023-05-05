<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Model;
/**
 * 菜单模型
 */
class AdminModel extends BaseModel
{
    public function getAdminByName($username)
    {
		$admin = Admin::where('username', $username)->first();
		return $admin;
    }
	public function passwordVerify($password, $admin_password)
	{
		return Util::passwordVerify($password, $admin_password);
	}
	public function updateLoginAt($admin);
	{
		$admin->login_at = date('Y-m-d H:i:s');
        $admin->save();
	}
}