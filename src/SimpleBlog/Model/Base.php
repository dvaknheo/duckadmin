<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace SimpleBlog\Model;

use DuckPhp\Foundation\SimpleModelTrait;

class Base
{
    use SimpleModelTrait;
    
    //public $table_name = null;

    public function get($id)
    {
        $sql = "select * from 'TABLE' where id =? and deleted_at is null";
        $sql = $this->prepare($sql);
        $ret = $this->fetch($sql, $id);
        return $ret;
    }
    public function delete($id)
    {
        $date = date('Y-m-d H:i:s');
        $sql = "update 'TABLE' set deleted_at=? where id=? ";
        $sql = $this->prepare($sql);
        $ret = $this->execute($sql, $date, $id);
        return $ret;
    }
}
