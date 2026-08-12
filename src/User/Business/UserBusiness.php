<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckAdmin\User\Business;

use DuckPhp\Foundation\Business\Helper;
use DuckPhp\Foundation\SingletonTrait;
use DuckAdmin\User\Model\UserModel;

/**
 * 我们偷懒，把 BusinessHelper 集成进这里,基类我们也不要了，毕竟只有一个
 * 绑定异常，
 */
class UserBusiness
{
    use SingletonTrait;

    public function log(int $user_id, string $string, ?string $type = null)
    {
        return;
    }
    public function checkAccess(int $id, string $class, string $method, ?string $url = null)
    {
        return;
    }
    
    public function register($form)
    {
        Helper::FireGlobalEvent("regist",$form);

        $form['password'] = $form['password'] ?? '';
        $form['password_confirm'] = $form['password_confirm'] ?? '';
        
        Helper::BusinessThrowOn($form['password'] != $form['password_confirm'], '重复密码不一致');

        $username = $form['name'];
        $password = $form['password'] ?? '';
        Helper::BusinessThrowOn($password === '', "密码为空");
        
        $flag = UserModel::_()->exsits($username);
        Helper::BusinessThrowOn($flag, "用户已经存在");
        
        $uid = UserModel::_()->addUser($username, $password);
        Helper::BusinessThrowOn(!$uid, "注册新用户失败");
        
        $user = UserModel::_()->getUserById($uid);
        $user = UserModel::_()->unloadPassword($user);
        Helper::FireGlobalEvent("registed",$user);
        return $user;
    }
    public function login($form)
    {
        Helper::FireGlobalEvent("logining",$form);

        $username = $form['name'];
        $password = $form['password'];
        $user = UserModel::_()->getUserByUsername($username);
        Helper::BusinessThrowOn(empty($user), "用户不存在");
        Helper::BusinessThrowOn(!empty($user['deleted_at']), "用户已不存在");
        
        $flag = UserModel::_()->verifyPassword($user, $password);
        Helper::BusinessThrowOn(!$flag, "密码错误");
        
        $user = UserModel::_()->unloadPassword($user);
        Helper::FireGlobalEvent("logined",$form);
        return $user;
    }
    public function changePassword($uid, $password, $new_password)
    {
        Helper::BusinessThrowOn($new_password === '', "空密码");
        $user = UserModel::_()->getUserById($uid);
        
        Helper::BusinessThrowOn(!empty($user['delete_at']), "用户已不存在");
        
        $flag = UserModel::_()->verifyPassword($user, $password);
        
        Helper::BusinessThrowOn(!$flag, "旧密码错误");
        
        UserModel::_()->updatePassword($uid, $new_password);
    }
    public function batchGetUsernames($ids)
    {
        return UserModel::_()->batchGetUsernames($ids);
    }
    public function getUserList()
    {
        //
    }
}
