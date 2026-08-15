<?php declare(strict_types=1);
/**
 * DuckPhp Admin System - App Action
 * Web 安装器(RouteHookWebInstaller)的自定义回调，Controller 层
 */
namespace DuckAdmin\Admin\Controller;

use DuckAdmin\Admin\Business\AdminBusiness;
use DuckPhp\Foundation\SingletonTrait;

class AppAction
{
    use SingletonTrait;

    /**
     * 安装前校验（web_installer_check_custom_callback）
     * @param array<string, mixed> $post
     * @return array<string, mixed>
     */
    public static function Callback_CheckInstall(array $post): array
    {
        return static::_()->checkInstall($post);
    }
    /**
     * 执行安装（web_installer_do_custom_callback）
     * @param array<string, mixed> $post
     * @param array<string, mixed> $ext_data
     * @return array<string, mixed>
     */
    public static function Callback_DoInstall(array $post, array $ext_data = []): array
    {
        return static::_()->doInstall($post, $ext_data);
    }

    /**
     * 校验安装表单：密码非空/两次一致/≥6 位，且尚未安装
     * @param array<string, mixed> $post
     * @return array<string, mixed>
     * @throws \Exception 校验失败时抛出，由 RouteHookWebInstaller 展示 custom_error_message
     */
    public function checkInstall(array $post): array
    {
        $admin_name = (string)($post['admin_name'] ?? '');
        $password = (string)($post['admin_password'] ?? '');
        $password_confirm = (string)($post['admin_password_confirm'] ?? '');

        if ($admin_name === '') {
            throw new \Exception('请填写管理员账号');
        }
        if ($password === '') {
            throw new \Exception('请填写管理员密码');
        }
        if (strlen($password) < 6) {
            throw new \Exception('管理员密码至少 6 位');
        }
        if ($password !== $password_confirm) {
            throw new \Exception('两次输入的密码不一致');
        }
        return [];
    }

    /**
     * 执行安装：委托 AdminBusiness
     * @param array<string, mixed> $post
     * @param array<string, mixed> $ext_data
     * @return array<string, mixed>
     */
    public function doInstall(array $post, array $ext_data = []): array
    {
        AdminBusiness::_()->install($post);
        return [];
    }
}
