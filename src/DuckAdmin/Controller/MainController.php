<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Controller;

use DuckAdmin\Business\InstallBusiness;
use DuckAdmin\System\ControllerHelper as C;

/**
 * 主入口
 */
class MainController extends Base
{
    /**
     * 无需登录的方法
     * @var string[]
     */
    protected $noNeedLogin = ['index'];

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
			C::Show([], 'index/install');
			return;
        }
        $admin =  AdminSession::G()->getCurrentAdmin();
        if (!$admin) {
			C::Show([], 'account/login');
            return ;
        }		
        C::Show([], 'index/index');
    }
	public function dashboard()
	{
		var_dump(DATE(DATE_ATOM));return;
		$dashboard=[];//AllInOnBusiness::G()->getDashboard();
		C::Show($dashboard, 'index/dashboard');
	}
}
