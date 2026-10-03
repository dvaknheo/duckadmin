<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckAdmin\User\Business;

use DuckPhp\Foundation\Business\Base;
use DuckAdmin\User\Model\UserModel;

class UserBusiness extends Base
{
    /**
     * 与 DuckPhp\GlobalUser\UserServiceInterface::log 对齐(含 $ext)
     * @param int|string $user_id
     * @param array<string, mixed> $ext
     */
    public function log($user_id, string $string, ?string $type = null, array $ext = [])
    {
        return;
    }
    /**
     * 参数顺序与 DuckPhp\GlobalUser\UserServiceInterface::canAccess 一致($url 在第二位)
     * @param int|string $user_id
     */
    public function canAccess($user_id, ?string $url = null, ?string $class = null, ?string $method = null): bool
    {
        return true;
    }
    /**
     * @param array<int, int|string> $ids
     * @return array<int|string, string>
     */
    public function batchGetUsernames(array $ids): array
    {
        return UserModel::_()->batchGetUsernames($ids);
    } 
    /**
     * 参数名/类型与 DuckPhp\GlobalUser\UserLoginServiceInterface::register(array $post) 对齐
     * (原来叫 $form 且无类型;覆盖率 DSL 是按参数名传参的,名字不一致会取不到值)
     * @param array<string, mixed> $post
     */
    public function register(array $post)
    {
        Helper::FireGlobalEvent("regist", $post);

        $post['password'] = $post['password'] ?? '';
        $post['password_confirm'] = $post['password_confirm'] ?? '';

        Helper::BusinessThrowOn($post['password'] != $post['password_confirm'], '重复密码不一致');

        $username = $post['name'];
        $password = $post['password'] ?? '';
        Helper::BusinessThrowOn($password === '', "密码为空");

        $flag = UserModel::_()->exsits($username);
        Helper::BusinessThrowOn($flag, "用户已经存在");

        $uid = UserModel::_()->addUser($username, $password);
        Helper::BusinessThrowOn(!$uid, "注册新用户失败");

        $user = UserModel::_()->getUserById($uid);
        $user = UserModel::_()->unloadPassword($user);
        $user['name'] = $user['username'];
        Helper::FireGlobalEvent("registed", $user);
        return $user;
    }
    /**
     * 参数名/类型与 DuckPhp\GlobalUser\UserLoginServiceInterface::login(array $post) 对齐
     * @param array<string, mixed> $post
     */
    public function login(array $post)
    {
        Helper::FireGlobalEvent("logining", $post);

        $username = $post['name'];
        $password = $post['password'];
        $user = UserModel::_()->getUserByUsername($username);
        Helper::BusinessThrowOn(empty($user), "用户不存在");
        Helper::BusinessThrowOn(!empty($user['deleted_at']), "用户已不存在");

        $flag = UserModel::_()->verifyPassword($user, $password);
        Helper::BusinessThrowOn(!$flag, "密码错误");

        $user = UserModel::_()->unloadPassword($user);
        $user['name'] = $user['username'];
        Helper::FireGlobalEvent("logined", $post);
        return $user;
    }
    public function logout($id)
    {
        //do nothing.
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
}
