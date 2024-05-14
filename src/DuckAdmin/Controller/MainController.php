<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Controller;

use DuckAdmin\Business\InstallBusiness;

/**
 * 主入口
 */
class MainController extends Base
{
    /**
     * 跳过验证，这里
     */
    public function __construct()
    {
        // 跳过验证，不做任何事
    }
    /**
     * 首页
     */
    public function index()
    {
        $admin = AdminAction::_()->getCurrentAdmin();
        if (!$admin) {
            Helper::Show([], 'account/login');
            return;
        }
        Helper::Show([], 'index/index');
    }
    public function dashboard()
    {
        var_dump(DATE(DATE_ATOM));return;
        $dashboard=[];//AllInOnBusiness::_()->getDashboard();
        return Helper::Show($dashboard, 'index/dashboard');
    }
}
