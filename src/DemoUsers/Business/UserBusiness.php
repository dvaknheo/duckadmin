<?php declare(strict_types=1);
/**
 * DuckAdmin DemoUsers - UserService
 * 预设用户系统:登录校验只允许 demo_users 数组中的用户(无数据库)
 */
namespace DuckAdmin\DemoUsers\Business;

use DuckPhp\Core\App;
use DuckPhp\Foundation\SingletonTrait;
use DuckPhp\GlobalUser\UserServiceInterface;

class UserBusiness implements UserServiceInterface
{
    use SingletonTrait;

    /**
     * 预设用户列表(下标 0 空占位,用户 id = 数组下标,禁止 id=0)
     * @return array<int, array<string, mixed>>
     */
    protected function getUserList(): array
    {
        return (array)(App::_()->options['demo_users'] ?? []);
    }

    /**
     * 校验用户名密码是否命中预设用户
     * @return array<string, mixed>|null ['id'=>int, 'username'=>string, 'name'=>string]
     */
    public function verifyLogin(string $username, string $password): ?array
    {
        foreach ($this->getUserList() as $id => $user) {
            if ((int)$id <= 0) {
                continue; // 跳过 id=0 占位
            }
            if (($user['username'] ?? '') === $username && ($user['password'] ?? '') === $password) {
                return [
                    'id' => (int)$id,
                    'username' => $username,
                    'name' => (string)($user['name'] ?? $username),
                ];
            }
        }
        return null;
    }

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
        foreach ($this->getUserList() as $id => $user) {
            if (in_array((int)$id, array_map('intval', $ids), true)) {
                $ret[$id] = (string)($user['username'] ?? '');
            }
        }
        return $ret;
    }
}
