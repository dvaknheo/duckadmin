<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckUserManager\Model;

use DuckPhp\Component\SimpleModelTrait;
use DuckPhp\Helper\ModelHelperTrait;

class UserModel
{
    use SimpleModelTrait;
    use ModelHelperTrait;
    public function getUserList($page = 1, $page_size = 10)
    {
        //
    }
    public function deleteUser($id)
    {
        //
    }
}