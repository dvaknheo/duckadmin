<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Admin\Controller;

use DuckPhp\Foundation\SingletonTrait;
use DuckPhp\GlobalAdmin\GlobalAdmin;

use DuckAdmin\Admin\Business\AppBusiness;

class AuthAction
{
    use SingletonTrait;
    public function __construct()
    {
    }
    public function login(array $post)
    {
        Helper::FireGlobalEvent(GlobalAdmin::EVENT_ACTION_ADMIN_LOGINING, $post);
        $admin = AppBusiness::_()->login($post);
        Session::_()->setCurrentAdmin($admin);
        Helper::FireGlobalEvent(GlobalAdmin::EVENT_ACTION_ADMIN_LOGED, $post);

        if (Helper::AppOptions('admin_loginout_auto_redirect')??true) {
            Helper::Show302(Helper::Admin()->urlForHome());
        }
    }
    public function logout()
    {
        $admin_id = Session::_()->getCurrentAdminId();
        Helper::FireGlobalEvent(GlobalAdmin::EVENT_ACTION_ADMIN_LOGOUTING, $admin_id);
        AppBusiness::_()->logout($admin_id);
        Session::_()->unsetCurrentAdmin();
        Helper::FireGlobalEvent(GlobalAdmin::EVENT_ACTION_ADMIN_LOGOUTED, $admin_id);
        if (Helper::AppOptions('admin_loginout_auto_redirect')??true) {
            Helper::Show302(Helper::Admin()->urlForLogin());
        }
    }
}
