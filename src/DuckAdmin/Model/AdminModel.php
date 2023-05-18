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
		return static::Db()->fetch("select * from wa_admin where username = ?",$username);

    }
	public function getAdminById($admin_id)
	{
		return static::Db()->fetch("select * from wa_admin where username = ?", $admin_id);
	}

	public function passwordVerify($password, $admin_password)
	{
		return \password_verify($password, $admin_password);
	}
	protected function passwordHash($password)
	{
		return \password_hash($password);
	}
	public function updateLoginAt($admin_id)
	{
		static::Db()->exec("update wa_admin set login_at =? where id=?",date('Y-m-d H:i:s'),$admin_id);
	}
	public function addFirstAdmin($username,$password)
	{
		$sql="insert into `wa_admins` (`username`, `password`, `nickname`, `created_at`, `updated_at`) values (:username, :password, :nickname, :created_at, :updated_at)";
		
		$password = $this->passwordHash($password);
		$time = date('Y-m-d H:i:s');
		static::Db()->exec($sql, $username,$password, '超级管理员',$time,$time);
        $admin_id = static::Db()->insertId();
		return $admin_id;
	}
	public function hasAdmins()
	{
		return static::Db()->fetchColumn("select count(*) as c from wa_admin");
	}
}