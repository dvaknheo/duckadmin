<?php declare(strict_types=1);
/**
 * DuckAdmin DemoUsers - UserService
 * 预设用户系统:登录校验只允许 demo_users 数组中的用户(无数据库)
 */
namespace DuckAdmin\DemoUsers\Business;

use DuckPhp\Foundation\SingletonTrait;
use DuckPhp\Foundation\Business\Helper;
use DuckPhp\GlobalUser\GlobalUser;
use DuckPhp\GlobalUser\UserServiceInterface;

class UserBusiness implements UserServiceInterface
{
    use SingletonTrait;

    public function canAccess($user_id, string $class, string $method, ?string $url = null): bool
    {
        return true;
    }
    /**
     * @param string $user_id
     */
    public function log($user_id, string $string, ?string $type = null, array $ext = [])
    {
        return;
    }

    public function batchGetUsernames(array $ids): array
    {
        $ret = [];
        $user_array = App::_()->options['demo_users'];
        $usernames = \array_keys($user_array);

        foreach ($usernames as $i => $name) {
            $id = $i+1;
            if (in_array((int)$id, array_map('intval', $ids), true)) {
                $ret[$id] = $name;
            }
        }
        return $ret;
    }
    /////////////
    public function login(array $post): ?array
    {
        Helper::FireGlobalEvent(GlobalUser::EVENT_SERVICE_USER_LOGINING, $post);
        
        $username = (string)($post['username']??'');
        $password = (string)($post['password']??'');

        $user_array = Helper::AppOptions('demo_users', []);
        
        $usernames = \array_keys($user_array);
        $passwords = \array_values($user_array);
        $id = \array_search($username, $usernames, true);
        Helper::ThrowOn($id === false, "没有这个用户名");
        Helper::ThrowOn(empty($passwords[$id]), "用户被禁用");
        Helper::ThrowOn($password !== $passwords[$id], "密码错误");

        $user = [
            'id'=>$id+1,
            'name'=>$username,
        ];
        Helper::FireGlobalEvent(GlobalUser::EVENT_SERVICE_USER_LOGINED, $user);
        return $user;
    }
    public function logout($id)
    {
        Helper::FireGlobalEvent(GlobalUser::EVENT_SERVICE_USER_LOGOUTING, $id);
        //only fire event
        Helper::FireGlobalEvent(GlobalUser::EVENT_SERVICE_USER_LOGOUTED, $id);
    }
    public function register(array $post)
    {
        //override do nothing, not fire event
    }

}
