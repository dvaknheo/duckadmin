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
	public $table_name = 'wa_admins';
	
    public function getAdminByName($username)
    {
		return static::Db()->fetch("select * from wa_admins where username = ?",$username);

    }
	public function getAdminById($admin_id)
	{
		$data = static::Db()->fetch("select * from wa_admins where id = ?", $admin_id);
		return $data;
	}

	public function passwordVerify($password, $admin_password)
	{
		return \password_verify($password, $admin_password);
	}
	protected function passwordHash($password)
	{
		return \password_hash($password, PASSWORD_DEFAULT);
	}
	public function updateLoginAt($admin_id)
	{
		static::Db()->execute("update wa_admins set login_at =? where id=?",date('Y-m-d H:i:s'),$admin_id);
	}
	public function addFirstAdmin($username,$password)
	{
		$sql="insert into `wa_admins` (`username`, `password`, `nickname`, `created_at`, `updated_at`) values (?, ?, ?, ?, ?)";
		
		$password = $this->passwordHash($password);
		$time = date('Y-m-d H:i:s');
		static::Db()->execute($sql, $username,$password, '超级管理员',$time,$time);
        $admin_id = static::Db()->lastInsertId();
		return $admin_id;
	}
	public function hasAdmins()
	{
		return static::Db()->fetchColumn("select count(*) as c from wa_admins");
	}
}