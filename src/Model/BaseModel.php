<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Model;

use DuckAdmin\App\BaseModel as Base;
use DuckAdmin\App\ModelHelper as M;

class BaseModel extends Base
{
    protected $table_name;
    protected function prepare($sql)
    {
        return str_replace('{TABLE}',$this->table_name, $sql);
    }
    public function getList(int $page = 1, int $page_size = 10)
    {
        $sql = "SELECT * from {$this->table_name} where true order by id desc";
        $total = M::Db()->fetchColumn(M::SqlForCountSimply($sql));
        $data = M::DB()->fetchAll(M::SqlForPager($sql, $page, $page_size));
        return ['data'=>$data,"total"=>$total];
    }
    public function get($id)
    {
        $sql = "select * from {$this->table_name} where id =?";
        $ret = M::DB()->fetch($sql, $id);
        return $ret;
    }
    public function add($data)
    {
        $ret = M::DB()->insertData($this->table_name, $data);
        return $ret;
    }
    public function update($id, $data)
    {
        $ret = M::DB()->updateData($this->table_name, $id, $data);
        
        return $ret;
    }
    public function delete($id)
    {
        $date = date('Y-m-d H:i:s');
        $sql = "update {$this->table_name} set deleted_at=? where id=? ";
        $ret = M::DB()->execute($sql, $date, $id);
        return $ret;
    }
}
