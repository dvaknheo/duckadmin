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
        if(Helper::IsPost()){
            $app_data['duckadmin_admin_provider'] = Helper::POST('admin');
            $app_data['duckadmin_user_provider'] = Helper::POST('user');
            $this->saveData($app_data);
        }

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
        Helper::Show($data,'main');
    }
    protected function saveData($data)
    {
        $keys = ['duckadmin_admin_provider', 'duckadmin_user_provider'];
        $app_keys = array_keys(DuckAdminApp::_()->options['app']);

        // 只接受合法的提供者名;非法/缺失的一侧保留原值(不能直接 unset 后照旧保存,
        // 那会把一个"没有这两个键的 data 段"写进配置文件,之后连纠正它的 POST 都会失败)
        $old = Helper::AppOptions('data', []);
        $valid = [];
        foreach ($keys as $k) {
            if (isset($data[$k]) && in_array($data[$k], $app_keys, true)) {
                $valid[$k] = $data[$k];
            } elseif (isset($old[$k]) && in_array($old[$k], $app_keys, true)) {
                $valid[$k] = $old[$k];
            }
        }
        if (count($valid) !== count($keys)) {
            // 两侧都凑不出合法值(例如表单没带字段),不写,避免写坏配置文件
            return;
        }
        ExtOptionsLoader::_()->saveExtOptions($valid);
    }
}