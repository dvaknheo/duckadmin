<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Model;

class AdminModel extends Base
{
    protected $table_name ='admin';

    public function login($username,$password)
    {
        $sql = "select * from 'TABLE' where username = ?";
        $data = Base::Db()->fetch($this->prepare($sql), $username);
        if(empty($data)){
            return [];
        }
        if(!password_verify($password, $data['password'])){
            return [];
        }
        unset($data['password']);

        return $data;
    }
    public function getList(int $page = 1, int $page_size = 10)
    {
        return parent::getList($page, $page_size);
    }
    //////////
    public function addData($data)
    {
        return Base::Db()->insertData($this->table(), $data);
    }
    public function updateData($id,$data)
    {
        return Base::Db()->updateData($this->table(), $id, $data);
    }

}
