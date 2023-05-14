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
		return password_hash($password);
	}
	public function updateLoginAt($admin_id)
	{
		static::Db()->exec("update wa_admin set login_at =? where id=?",date('Y-m-d H:i:s'),$admin_id);
	}
	public function addFirstAdmin($username,$password)
	{
        $smt = $pdo->prepare("insert into `wa_admins` (`username`, `password`, `nickname`, `created_at`, `updated_at`) values (:username, :password, :nickname, :created_at, :updated_at)");
        $time = date('Y-m-d H:i:s');
        $data = [
            'username' => $username,
            'password' => $this->passwordHash($password),
            'nickname' => '超级管理员',
            'created_at' => $time,
            'updated_at' => $time
        ];
        foreach ($data as $key => $value) {
            $smt->bindValue($key, $value);
        }
        $smt->execute();
        $admin_id = $pdo->lastInsertId();
		return $admin_id;
	}
}