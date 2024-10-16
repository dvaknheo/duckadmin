<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckUserManager\Model;

use DuckPhp\Foundation\SimpleModelTrait;
use DuckPhp\Foundation\Model\Helper;

class UserModel
{
    use SimpleModelTrait;
    public function __construct()
    {
        $this->table_name ="Users";
    }
    public function getUserList($where=[], $page = 1, $page_size = 10)
    {
        $is_all = $where['all']? true:false;
        $sql_where = $is_all ? ' TRUE ' : ' deleted_at IS NULL';
        
        $sql = "SELECT * FROM `'TABLE'` WHERE $sql_where ORDER BY id DESC";
        $sql = $this->prepare($sql);
        
        $total = $this->fetchColumn(Helper::SqlForCountSimply($sql));
        $data = $this->fetchAll(Helper::SqlForPager($sql, $page, $page_size));
        return [$total, $data];
    }
    public function deleteUser($id)
    {
        $sql = "UPDATE `'TABLE'` SET deleted_at = ? WHERE id =? AND deleted_at IS NULL";
        $ret = $this->execute($sql,date('Y-m-d H:i:s'),$id);
        return $ret;
    }
    public function unDeleteUser($id)
    {
        $sql = "UPDATE `'TABLE'` SET deleted_at = NULL WHERE id =?"; //AND deleted_at IS NOT NULL
        $ret = $this->execute($sql,$id);
        return $ret;
    }
}