<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckUser\Model;

use DuckPhp\Foundation\SimpleModelTrait;
use DuckPhp\Foundation\Model\Helper;

class UserModel
{
    use SimpleModelTrait;
    
    public function __construct()
    {
        $this->table_name = "Users";
    }
    public function exsits($name)
    {
        $sql = "select count(*) as c from `'TABLE'` where username=?";
        
        $count = $this->fetchColumn($sql, $name);
        return !empty($count)?true:false;
    }
    public function addUser($username, $password)
    {
        $data = [];
        $data['username'] = $username;
        $data['password'] = $this->hash($password);
        $id = $this->add($data);
        return $id;
    }
    public function getUserById($id)
    {
        $sql = "select * from `'TABLE'` where id=?";
        $user = $this->fetch($sql, $id);
        
        return $user;
    }
    public function getUserByUsername($username)
    {
        $sql = "select * from `'TABLE'` where username=?";
        $user = $this->fetch($sql, $username);
        
        return $user;
    }
    public function batchGetUsernames($user_ids)
    {
        if(empty($user_ids)){ return []; }
        $user_ids = Helper::DbForRead()->quoteIn($user_ids);
        $sql = "select id,username from `'TABLE'` where id in ($user_ids)";
        $data = $this->fetchAll($sql);
        $ret = array_column($data,'username','id');
        return $ret;
    }
    public function verifyPassword($user, $password)
    {
        return $this->verify($password, $user['password']);
    }
    public function unloadPassword($user)
    {
        unset($user['password']);
        return $user;
    }
    public function updatePassword($uid, $password)
    {
        $password = $this->hash($password);
        $sql = "update `'TABLE'` set password=? where id=? limit 1";
        $ret = $this->execute($sql, $password, $uid);
        return $ret;
    }
    /////
    protected function hash($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }
    /////
    protected function verify($password, $hash)
    {
        return password_verify($password, $hash);
    }
}
