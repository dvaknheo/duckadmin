<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Model;
use DuckAdmin\App\ModelHelper as M;

class AdminModel extends BaseModel
{
    protected $table_name ='admin';

    public function login($username,$password)
    {
        $sql = "select * from 'TABLE' where username = ?";
        $data = M::Db()->fetch($this->prepare($sql), $username);
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

}
