<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace SimpleBlog\Model;

use DuckPhp\Foundation\SimpleModelTrait;
use DuckPhp\Helper\ModelHelperTrait;

class Base
{
    use SimpleModelTrait;
    use ModelHelperTrait;
    
    //public $table_name = null;

    public function get($id)
    {
        //override as public
        $sql = "select * from `'TABLE'` where id =? and deleted_at is null";
        $ret = $this->fetch($sql, $id);
        return $ret;
    }
    public function delete($id)
    {
        $date = date('Y-m-d H:i:s');
        $sql = "update `'TABLE'` set deleted_at=? where id=? ";
        $ret = $this->execute($sql, $date, $id);
        return $ret;
    }
    protected function getList($where = [], int $page = 1, int $page_size = 10)
    {
        $include_del = $where['deleted_at'] ??false;
        if(!$include_del){
           unset($where['deleted_at']);
        }
        
        $sql_where = self::DbForRead()->quoteAndArray($where);
        $sql_where = $sql_where?:' TRUE ';
        
        if(!$include_del){
            $sql_where .="and deleted_at is null";  // ugly
        }
        $sql = "SELECT * from `'TABLE'` where $sql_where order by id desc";
        $sql = $this->prepare($sql);
        
        $total = self::DbForRead()->fetchColumn(self::SqlForCountSimply($sql));
        $data = self::DbForRead()->fetchAll(self::SqlForPager($sql, $page, $page_size));
        
        
        return [$total, $data];
    }
}
