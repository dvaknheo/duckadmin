<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckUserManager\Model;

use DuckPhp\Foundation\SimpleModelTrait;

class UserModel
{
    use SimpleModelTrait;
    public function __construct()
    {
        $this->table_name ="Users";
    }
    
    public function getUserList($where=[], $page = 1, $page_size = 10)
    {
     
        $ret = $this->getList($where, $page, $page_size);
        return $ret;
    }
    public function changeUserStatus($id,$stat)
    {
        return 1;
    }
}