<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Model;

class AdminModel extends BaseModel
{
    protected $table ='admin';
    public function login($username,$password)
    {
        $sql = "select * from TABLE where username = ?";
        $data = __db()->fetch($this->prepare($sql), $username);
        if(empty($data)){

            return [];
        }
        if(!password_verify($password, $data['password'])){
            return [];
        }
        unset($data['password']);

        return $data;
    }
    
    public function add()
    {
        //
    }
}
