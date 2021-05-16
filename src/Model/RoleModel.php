<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Model;
use DuckAdmin\App\ModelHelper as M;

class RoleModel extends BaseModel
{
    
    public function getRoleName($id)
    {
        $sql = "SELECT name from 'TABLE' where id = ?";
        $sql = $this->prepare($sql);
        return M::Db()->fetchColumn($sql,$id);
    }
    public function getRoles()
    {
        $sql = "SELECT id,name from 'TABLE' order by id";
        $sql = $this->prepare($sql);
        $data = M::Db()->fetchAll($sql);
        $ret = array_column($data,'name','id');
        ksort($ret);
        return $ret;
    }
}
