<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Controller;

use DuckAdmin\Business\InstallBusiness;
use DuckAdmin\Controller\AdminAction as C;

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
			return C::Show([], 'index/install');
        }
        $admin = AdminSession::G()->getCurrentAdmin();
        if (!$admin) {
			return C::Show([], 'account/login');
        }		
        return C::Show([], 'index/index');
    }
}
