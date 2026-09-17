<?php declare(strict_types=1);
/**
 * DuckPhp Admin System - App Action
 * Web 安装器(RouteHookWebInstaller)的自定义回调，Controller 层
 */
namespace DuckAdmin\Admin\Controller;

use DuckAdmin\Admin\Business\AppBusiness;
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
        return AppBusiness::_()->checkInstall($post);
    }

    /**
     * 执行安装：委托 AdminBusiness
     * @param array<string, mixed> $post
     * @param array<string, mixed> $ext_data
     * @return array<string, mixed>
     */
    public function doInstall(array $post, array $ext_data = []): array
    {
        AppBusiness::_()->install($post);
        return [];
    }
    ////////////////////////////////////////
    //@override
    public function localService()
    {
        return AppBusiness::_();
    }
    public function session()
    {
        return Session::_();
    }

    //@override
    public function addExtViewData(array $input): array
    {
        $input['app_name'] = 'Admin System';
        $input['current_user'] = Session::_()->getCurrentAdmin();
        $input['menus'] = $this->menu();
        
        return $input;
    }
    public function menu()
    {
        $admin_id = Session::_()->getCurrentAdminId();
        return AppBusiness::_()->menu($admin_id);
    }
    public function command_rebuild_menu()
    {
        \DuckAdmin\Admin\Business\AdminTreeBuilder::_()->buildAndSaveToConfigJsonFile();
        echo "done: "; 
        echo DATE(DATE_ATOM);
        echo PHP_EOL;
    }
    /**
     * test something
     */
    public function command_test()
    {
        $tree = \DuckAdmin\Admin\Business\TestBusiness::_()->test();
        echo json_encode($tree,JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
