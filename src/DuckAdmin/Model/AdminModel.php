<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Model;
/**
 * 菜单模型
 */
class AdminModel extends Base
{
    public $table_name = 'wa_admins';
    
    public function passwordVerify($password, $admin_password)
    {
        return \password_verify($password, $admin_password);
    }
    protected function passwordHash($password)
    {
        return \password_hash($password, PASSWORD_DEFAULT);
    }
    // 这和父类不同
    public function inputFilter(array $data): array
    {
        $data = parent::inputFilter($data);
        $password_filed = 'password';
        if (isset($data[$password_filed])) {
            $data[$password_filed] = $this->passwordHash($data[$password_filed]);
        }
        return $data;
    }
    
    //// select
    public function selectInput($data): array
    {
        // 隔离BaseModel 的调用
        return parent::selectInput($data);
    }
    public function doSelect(array $where, string $field = null, string $order= 'desc' ,$page=1,$page_size=10)
    {
        // 隔离BaseModel 的调用
        return parent::doSelect($where, $field, $order,$page,$page_size);
    }

    public function getAdminByName($username)
    {
        return static::Db()->fetch("select * from wa_admins where username = ?",$username);

    }
    public function getAdminById($admin_id)
    {
        $data = static::Db()->fetch("select * from wa_admins where id = ?", $admin_id);
        return $data;
    }

    
    public function hasAdmins()
    {
        return static::Db()->fetchColumn("select count(*) as c from wa_admins");
    }
    //// delete
    public function deleteByIds($ids)
    {
        $sql ="delete from wa_admins where id in(".static::Db()->quoteIn($ids).")";
        static::execute($sql);
    }
    //// insert
    public function addFirstAdmin($username,$password)
    {
        $sql="insert into `wa_admins` (`username`, `password`, `nickname`, `created_at`, `updated_at`) values (?, ?, ?, ?, ?)";
        
        $password = $this->passwordHash($password);
        $time = date('Y-m-d H:i:s');
        static::Db()->execute($sql, $username,$password, '超级管理员',$time,$time);
        $admin_id = static::Db()->lastInsertId();
        return $admin_id;
    }
    public function addAdmin(array $data)
    {
        //$data['`key`']=$data['key'];
        //unset($data['key']);
        
        $time = date('Y-m-d H:i:s');
        $data['created_at']=$time;
        $data['updated_at']=$time;
        static::Db()->insertData($this->table(),$data);
        return 	static::Db()->lastInsertId();
    }
    //// update
    public function updateLoginAt($admin_id)
    {
        static::Db()->execute("update wa_admins set login_at =? where id=?",date('Y-m-d H:i:s'), $admin_id);
    }
    
    public function updateAdmin($admin_id, $data)
    {
        $allow_column = [
            'nickname' => 'nickname',
            'avatar' => 'avatar',
            'email' => 'email',
            'mobile' => 'mobile',
            'username' => 'username',
            'status' =>'status',
        ];

        $update_data = [];
        foreach ($allow_column as $key => $column) {
            if (isset($data[$key])) {
                $update_data[$column] = $data[$key];
            }
        }
        static::Db()->updateData($this->table(),$admin_id,$update_data, 'id');
        return $update_data;
    }
    public function checkPasswordByAdmin($admin, $password)
    {
        return $this->passwordVerify($password, $admin['password']);
    }
    public function checkPassword($admin_id, $password)
    {
        $admin = $this->getAdminById($admin_id);
        return $this->passwordVerify($password, $admin['password']);
    }
    public function changePassword($admin_id,$password)
    {
        $update_data = [
            'password' => $this->passwordHash($password),
        ];
        static::Db()->updateData($this->table(),$admin_id,$update_data, 'id');
    }
}