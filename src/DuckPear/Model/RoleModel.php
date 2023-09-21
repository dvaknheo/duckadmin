<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckPear\Model;
/**
 * 
 */
class RoleModel extends BaseModel
{
    public function getRoleName($id)
    {
        $sql = "SELECT name from 'TABLE' where id = ?";
        return $this->fetchColumn($sql,$id);
    }
    public function getRoles()
    {
        $sql = "SELECT id,name from 'TABLE' order by id";

        $data = $this->fetchAll($sql);
        $ret = array_column($data,'name','id');
        ksort($ret);
        return $ret;
    }
}
