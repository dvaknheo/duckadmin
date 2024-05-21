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
    
    public function getUserList($conditions=[], $page = 1, $page_size = 10)
    {
        return [[],1];
    }
    public function changeUserStatus($id,$stat)
    {
        return 1;
    }
}