<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckUser\Business;

use DuckPhp\Lazy\BusinessTrait;

use DuckUser\Business\UserBusiness as Helper;
use DuckUser\Model\UserModel;
use DuckUser\System\ProjectException;

/**
 * 我们偷懒，把 BusinessHelper 集成进这里,基类我们也不要了，毕竟只有一个
 * 绑定异常，偷懒
 */
class UserBusiness
{
    use BusinessTrait;
    
    public function __construct()
    {
        // 绑定 ThrowOn 的异常类
        $this->exception_class = $this->exception_class ?? ProjectException::class;
    }
    public function register($form)
    {
        $form['password'] = $form['password'] ?? '';
        $form['password_confirm'] = $form['password_confirm'] ?? '';
        
        static::ThrowOn($form['password'] != $form['password_confirm'], '重复密码不一致');

        $username = $form['name'];
        $password = $form['password'] ?? '';
        static::ThrowOn($password === '', "密码为空");
        
        $flag = UserModel::G()->exsits($username);
        static::ThrowOn($flag, "用户已经存在");
        
        $uid = UserModel::G()->addUser($username, $password);
        static::ThrowOn(!$uid, "注册新用户失败");
        
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
        static::ThrowOn(empty($user), "用户不存在");
        static::ThrowOn(!empty($user['delete_at']), "用户已被禁用");
        
        $flag = UserModel::G()->verifyPassword($user, $password);
        static::ThrowOn(!$flag, "密码错误");
        
        $user = UserModel::G()->unloadPassword($user);
        Helper::FireEvent([self::class, __METHOD__],$user);
        return $user;
    }
    public function changePassword($uid, $password, $new_password)
    {
        static::ThrowOn($new_password === '', "空密码");
        $user = UserModel::G()->getUserById($uid);
        
        static::ThrowOn(!empty($user['delete_at']), "用户已被禁用");
        
        $flag = UserModel::G()->verifyPassword($user, $password);
        
        static::ThrowOn(!$flag, "旧密码错误");
        
        UserModel::G()->updatePassword($uid, $new_password);
    }
}
