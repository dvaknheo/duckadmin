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
     * 无需登录的方法
     * @var string[]
     */
    protected $noNeedLogin = ['index','test'];

    /**
     * 不需要鉴权的方法
     * @var string[]
     */
    protected $noNeedAuth = ['dashboard'];

    /**
     * 首页
     */
    public function index()
    {
		$isInstalled = InstallBusiness::G()->IsInstalled();
        if (!$isInstalled) {
            Helper::Show([], 'index/install');
            return;
        }
        $admin = AdminAction::G()->getCurrentAdmin();
        if (!$admin) {
            Helper::Show([], 'account/login');
            return;
        }
        Helper::Show([], 'index/index');
    }
}
