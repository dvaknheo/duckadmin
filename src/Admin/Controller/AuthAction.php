<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Admin\Controller;

use DuckPhp\Foundation\SingletonTrait;

use DuckAdmin\Admin\Business\AppBusiness;

class AuthAction
{
    use SingletonTrait;
    public function __construct()
    {
    }
    public function login(array $post)
    {
        Helper::FireGlobalEvent(Helper::EVENT_ADMIN_LOGINING, $post);
        $admin = AppBusiness::_()->login($post);
        Session::_()->setCurrentAdmin($admin);
        Helper::FireGlobalEvent(Helper::EVENT_ADMIN_LOGED, $post);

        if (Helper::AppOptions('admin_loginout_auto_redirect')??true) {
            Helper::Show302(Helper::Admin()->urlForHome());
        }
    }
    public function logout()
    {
        $admin_id = Session::_()->getCurrentAdminId();
        Helper::FireGlobalEvent(Helper::EVENT_ADMIN_LOGOUTING, $admin_id);
        AppBusiness::_()->logout($admin_id);
        Session::_()->unsetCurrentAdmin();
        Helper::FireGlobalEvent(Helper::EVENT_ADMIN_LOGOUTED, $admin_id);
        if (Helper::AppOptions('admin_loginout_auto_redirect')??true) {
            Helper::Show302(Helper::Admin()->urlForLogin());
        }
    }
}
