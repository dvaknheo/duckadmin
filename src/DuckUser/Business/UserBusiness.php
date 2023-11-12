<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckUser\Business;

use DuckUser\Model\UserModel;

/**
 * 我们偷懒，把 BusinessHelper 集成进这里,基类我们也不要了，毕竟只有一个
 * 绑定异常，
 */
class UserBusiness extends Base
{
    public function register($form)
    {
        $form['password'] = $form['password'] ?? '';
        $form['password_confirm'] = $form['password_confirm'] ?? '';
        
        BusinessException::ThrowOn($form['password'] != $form['password_confirm'], '重复密码不一致');

        $username = $form['name'];
        $password = $form['password'] ?? '';
        BusinessException::ThrowOn($password === '', "密码为空");
        
        $flag = UserModel::G()->exsits($username);
        BusinessException::ThrowOn($flag, "用户已经存在");
        
        $uid = UserModel::G()->addUser($username, $password);
        BusinessException::ThrowOn(!$uid, "注册新用户失败");
        
        $user = UserModel::G()->getUserById($uid);
        $user = UserModel::G()->unloadPassword($user);
        Helper::FireEvent([self::class, __METHOD__],$user);
        return $user;
    }
    public function login($form)
    {
        $username = $form['name'];
        $password = $form['password'];
        $user = UserModel::G()->getUserByUsername($username);
        BusinessException::ThrowOn(empty($user), "用户不存在");
        BusinessException::ThrowOn(!empty($user['delete_at']), "用户已被禁用");
        
        $flag = UserModel::G()->verifyPassword($user, $password);
        BusinessException::ThrowOn(!$flag, "密码错误");
        
        $user = UserModel::G()->unloadPassword($user);
        Helper::FireEvent([self::class, __METHOD__],$user);
        return $user;
    }
    public function changePassword($uid, $password, $new_password)
    {
        BusinessException::ThrowOn($new_password === '', "空密码");
        $user = UserModel::G()->getUserById($uid);
        
        BusinessException::ThrowOn(!empty($user['delete_at']), "用户已被禁用");
        
        $flag = UserModel::G()->verifyPassword($user, $password);
        
        BusinessException::ThrowOn(!$flag, "旧密码错误");
        
        UserModel::G()->updatePassword($uid, $new_password);
    }
}
