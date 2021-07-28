<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Model;

use DuckAdmin\System\ProjectModel;

class Base extends ProjectModel
{    
    protected $table_name=null;
    protected function table()
    {
        if(!isset($this->table_name)){
            $this->table_name = self::GetTableByClass(static::class);
        }
        return $this->table_name;
    }
    public function getList(int $page = 1, int $page_size = 10)
    {
        $sql = "SELECT * from 'TABLE' where true order by id desc";
        $sql = $this->prepare($sql);
        
        $total = self::Db()->fetchColumn(self::SqlForCountSimply($sql));
        $data = self::Db()->fetchAll(self::SqlForPager($sql, $page, $page_size));
        return ['data'=>$data,"total"=>$total];
    }
    public function get($id)
    {
        $sql = "select * from 'TABLE' where id =?";
        $sql = $this->prepare($sql);
        $ret = self::Db()->fetch($sql, $id);
        return $ret;
    }
    public function find($a)
    {
        $f=[];
        foreach($a as $k => $v){
            $f[]= $k . ' = ' . self::Db()->quote($v);
        }
        $frag=implode('and ',$f);
        
        $sql = "select * from 'TABLE' where ".$frag;
        $sql = $this->prepare($sql);
        $ret = self::Db()->fetch($sql, $id);
        return $ret;
    }
    public function add($data)
    {
        $ret = self::Db()->insertData($this->table(), $data);
        return $ret;
    }
    public function update($id, $data)
    {
        $ret = self::Db()->updateData($this->table(), $id, $data);
        
        return $ret;
    }
    public function delete($id)
    {
        $date = date('Y-m-d H:i:s');
        $sql = "update 'TABLE' set deleted_at=? where id=? ";
        $sql = $this->prepare($sql);
        $ret = self::Db()->execute($sql, $date, $id);
        return $ret;
    }
}
