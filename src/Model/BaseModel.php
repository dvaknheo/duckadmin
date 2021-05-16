<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Model;

use DuckAdmin\App\SingletonExTrait;
use DuckAdmin\App\ModelHelper as M;
use DuckAdmin\App\App;

class BaseModel
{
    use SingletonExTrait;
    //use ModelHelperTrait;
    
    protected $table_name=null;
    
    protected function table()
    {
        if(!isset($this->table_name)){
            $t = explode('\\',static::class);
            $class = array_pop($t);
            
            $table_name = 'admin_'.strtolower(substr($class,0,-5));
            $table_name = ($table_name==='admin_admin')? 'admin' : $table_name;
            $table_name = App::Setting('duckadmin_pre_prefix').$table_name;
            $this->table_name = $table_name;
        }
        return $this->table_name;
    }
    protected function prepare($sql)
    {
        // 用单引号 是因为正常 状态下不会转码
        return str_replace("'TABLE'",$this->table(), $sql);
    }
    public function getList(int $page = 1, int $page_size = 10)
    {
        $sql = "SELECT * from 'TABLE' where true order by id desc";
        $sql = $this->prepare($sql);
        
        $total = M::Db()->fetchColumn(M::SqlForCountSimply($sql));
        $data = M::Db()->fetchAll(M::SqlForPager($sql, $page, $page_size));
        return ['data'=>$data,"total"=>$total];
    }
    public function get($id)
    {
        $sql = "select * from 'TABLE' where id =?";
        $sql = $this->prepare($sql);
        $ret = M::Db()->fetch($sql, $id);
        return $ret;
    }
    public function find($a)
    {
        $f=[];
        foreach($a as $k => $v){
            $f[]= $k . ' = ' . M::Db()->quote($v);
        }
        $frag=implode('and ',$f);
        
        $sql = "select * from 'TABLE' where ".$frag;
        $sql = $this->prepare($sql);
        $ret = M::Db()->fetch($sql, $id);
        return $ret;
    }
    public function add($data)
    {
        $ret = M::DB()->insertData($this->table(), $data);
        return $ret;
    }
    public function update($id, $data)
    {
        $ret = M::DB()->updateData($this->table(), $id, $data);
        
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
