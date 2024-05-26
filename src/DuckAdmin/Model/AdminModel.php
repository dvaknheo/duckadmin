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
    protected $table_name = 'admins';
    
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
        // 这里要去除密码行
        $ret =parent::doSelect($where, $field, $order,$page,$page_size);
        foreach($ret[0] as &$v){
            unset($v['password']);
        }
        return $ret;
    }
    public function foo($id, $input)
    {
        [$where, $format, $limit, $field, $order, $page] = AdminModel::_()->selectInput($input);
        
        // 这里要限定属于自己的 role 下的
        
        [$items,$total] = AdminModel::_()->doSelect($where, $field, $order,$page,$limit);
        
    }
    

    public function getAdminByName($username)
    {
        return $this->fetch("select * from `'TABLE'` where username = ?",$username);

    }
    public function getAdminById($admin_id)
    {
        $data = $this->fetch("select * from `'TABLE'` where id = ?", $admin_id);
        return $data;
    }

    
    public function hasAdmins()
    {
        return $this->fetchColumn("select count(*) as c from wa_admins");
    }
    //// delete
    public function deleteByIds($ids)
    {
        $sql ="delete from `'TABLE'` where id in(".static::Db()->quoteIn($ids).")";
        static::execute($sql);
    }
    //// insert
    public function addFirstAdmin($username,$password)
    {
        $sql="insert into `'TABLE'` (`id`,`username`, `password`, `nickname`, `created_at`, `updated_at`) values (1, ?, ?, ?, ?, ?)";
        
        $password = $this->passwordHash($password);
        $time = date('Y-m-d H:i:s');
        $this->execute($sql, $username,$password, '超级管理员',$time,$time);
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
        $this->add($data);
        return	static::Db()->lastInsertId();
    }
    //// update
    public function updateLoginAt($admin_id)
    {
        $this->execute("update `'TABLE'` set login_at =? where id=?",date('Y-m-d H:i:s'), $admin_id);
    }
    
    public function updateAdmin($admin_id, $data)
    {
        $allow_column = [
            'nickname' => 'nickname',
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
        $this->update($admin_id,$update_data, 'id');
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
        $this->update($admin_id,$update_data, 'id');
    }
}