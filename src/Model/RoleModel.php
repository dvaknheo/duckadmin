<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Model;

class RoleModel extends Base
{
    public function getRoleName($id)
    {
        $sql = "SELECT name from 'TABLE' where id = ?";
        $sql = $this->prepare($sql);
        
        return Base::Db()->fetchColumn($sql,$id);
    }
    public function getRoles()
    {
        $sql = "SELECT id,name from 'TABLE' order by id";
        $sql = $this->prepare($sql);
        
        $data = Base::Db()->fetchAll($sql);
        $ret = array_column($data,'name','id');
        ksort($ret);
        return $ret;
    }
}
