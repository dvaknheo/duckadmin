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
        $admin_id = AdminAction::_()->getCurrentAdminId();
        if (!$admin_id) {
            Helper::Show([], 'account/login');
            return;
        }
        Helper::Show([], 'index/index');
    }
    public function dashboard()
    {
        // 这里要验证， 捂脸
        $data = [];
        Helper::Show($data, 'index/dashboard');
    }
}
