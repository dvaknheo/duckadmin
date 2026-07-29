<?php declare(strict_types=1);
/**
 * DuckPhp Admin System - Auth Business
 * 无状态：仅做密码校验，不涉及 Session
 */
namespace DuckAdmin\Admin\Business;

use DuckAdmin\Admin\Model\AdminUserModel;

class AuthBusiness extends Base
{
    /**
     * 验证用户名密码
     * @return array ['success' => bool, 'message' => string, 'user_id' => ?int, 'realname' => ?string]
     */
    public function verify(string $username, string $password): array
    {
        $user = AdminUserModel::_()->getByUsername($username);
        if (!$user) {
            return ['success' => false, 'message' => '用户名或密码错误1'];
        }
        // SQLite 返回字符串，用 == 比较
        if ($user['status'] == 0) {
            return ['success' => false, 'message' => '该账号已被禁用'];
        }
        //$password = '123456';
        //$hash = '$2y$10$zt0AFofgXHZ4u8aCVa3Uv.2R..oi5MGZ1yUnF8PLyWJgi0oybmjQG';
        
        if (!password_verify($password, $user['password'])) {

            
            return ['success' => false, 'message' => '用户名或密码错误2:'];
        }
        
        // 更新最后登录时间
        AdminUserModel::_()->updateLoginTime((int)$user['id']);
        
        return [
            'success' => true,
            'message' => '登录成功',
            'user_id' => (int)$user['id'],
            'realname' => $user['realname'] ?? $username,
        ];
    }
}
