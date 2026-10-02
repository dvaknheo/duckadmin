<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */
namespace DuckAdmin\System;

use DuckPhp\Foundation\Controller\ControllerHelper as Helper;
use DuckPhp\Component\ExtOptionsLoader;

class MainController
{
    public function index()
    {
        $data = [];

        // 获取管理员状态
        $data['admin_id'] = Helper::AdminId(false);
        $data['admin_name'] = Helper::AdminName(false);
        $data['url_admin_login'] = Helper::Admin()->urlForLogin();
        $data['url_admin_logout'] = Helper::Admin()->urlForLogout();
        $data['url_admin_home'] = Helper::Admin()->urlForHome();

        // 获取用户状态
        $data['user_id'] = Helper::UserId(false);
        $data['user_name'] = Helper::UserName(false);
        $data['url_user_login'] = Helper::User()->urlForLogin();
        $data['url_user_logout'] = Helper::User()->urlForLogout();
        $data['url_user_register'] = Helper::User()->urlForRegister();
        $data['url_user_home'] = Helper::User()->urlForHome();

        // 子应用入口
        $data['url_blog'] = __url(\DuckAdmin\SimpleBlog\System\SimpleBlogApp::_()->options['controller_url_prefix']);

        $app_data = Helper::AppOptions('data', []);
        $data['admin_app'] = $app_data['duckadmin_admin_provider'] ?? '';
        $data['user_app'] = $app_data['duckadmin_user_provider'] ?? '';
        if(Helper::IsPost()){
            $app_data['duckadmin_admin_provider'] = Helper::POST('admin');
            $app_data['duckadmin_user_provider'] = Helper::POST('user');
            $this->saveData($app_data);
        }
        Helper::Show($data,'main');
    }
    protected function saveData($data)
    {
        if(!in_array($data['duckadmin_admin_provider'], array_keys(DuckAdminApp::_()->options['app']))) {
            unset($data['duckadmin_admin_provider']);
        }
        if(!in_array($data['duckadmin_user_provider'], array_keys(DuckAdminApp::_()->options['app']))) {
            unset($data['duckadmin_user_provider']);
        }
        ExtOptionsLoader::_()->saveExtOptions($data);
    }
}