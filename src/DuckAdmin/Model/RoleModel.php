<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Model;
/**
 * 菜单模型
 */
class RoleModel extends Base
{
    protected $table_name = 'roles';
    protected $table_pk = 'id';

    public function selectInput($data): array
    {
        // 隔离BaseModel 的调用
        return parent::selectInput($data);
    }
    public function doSelect(array $where, string $field = null, string $order= 'desc' ,$page=1,$page_size=10)
    {
        // 隔离BaseModel 的调用
        return parent::doSelect($where, $field, $order,$page,$page_size);
    }
    public function inputFilter(array $data): array
    {
        // 隔离BaseModel 的调用
        return parent::inputFilter($data);
    }

    public function getRules($roles)
    {
        $sql="select rules from `'TABLE'` where id in (". static::Db()->quoteIn($roles).')';
        $data = $this->fetchAll($sql);
        $data = array_column($data,'rules');
        $ret=[];
        foreach($data as $v){
            if(!$v){continue;}
            $t=explode(',',$v);
            $ret = array_merge($ret,$t);
        }
        //var_dump($ret);
        return $ret;
    }
    
    public function getAllId()
    {
        $sql="select id from `'TABLE'`";
        $data = $this->fetchAll($sql);
        return array_column($data,'id');
    }
    public function getAll()
    {
        $sql="select * from `'TABLE'`";
        $data = $this->fetchAll($sql);
        return $data;
    }
    public function getById($id)
    {
        $sql="select * from `'TABLE'` where id = ?";
        $data = $this->fetch($sql,$id);
        return $data;
    }
    public function getRulesByRoleId($role_id)
    {
        $sql = "select rules from `'TABLE'` where id = ?";
        $data = $this->fetchColumn($sql,$role_id);
        return $data;
    }
    public function getAllIdPid()
    {
        $sql = "select id,pid from `'TABLE'`";
        $data = $this->fetchAll($sql);
        return $data;
    }
    public function deleteByIds($ids)
    {
        $sql="delete from `'TABLE'` where id in (" . static::Db()->quoteIn($ids).')';
        $this->execute($sql);
    }
    public function addRole($data)
    {
        $time = date('Y-m-d H:i:s');
        $data['created_at']=$time;
        $data['updated_at']=$time;
        $this->add($data);
        
        return static::Db()->lastInsertId();
    }
    public function updateRole($id, $data)
    {
        $time = date('Y-m-d H:i:s');
        $data['updated_at'] = $time;
        return $this->update($id, $data, 'id');
    }
    public function updateRoleMore($descendant_role_ids,$rule_ids)
    {
        foreach ($descendant_role_ids as $role_id) {
            $data = static::Db()->fetch("select * from `'TABLE'` where id = ? ",$role_id);
            $data['rules'] = implode(',', array_intersect(explode(',',$data['rules']),$rule_ids));
            $time = date('Y-m-d H:i:s');
            $data['updated_at'] = $time;
            $this->update($id, $data, 'id');
        }
    }
    
    public function addFirstRole()
    {
        $sql = "INSERT INTO `'TABLE'` VALUES (1,'超级管理员','*','2022-08-13 16:15:01','2022-12-23 12:05:07',NULL);";
        $this->execute($sql);
    }

}