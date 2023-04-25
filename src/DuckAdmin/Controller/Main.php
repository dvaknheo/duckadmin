<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Controller;

use DuckAdmin\Business\AdminBusiness;
use DuckAdmin\Controller\Base as C;
use DuckAdmin\ControllerEx\AdminSession;
use DuckAdmin\ControllerEx\CaptchaAction;
/**
 * 主入口
 */
class Main extends Base
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
		C::Show(get_defined_vars(), 'index/index');return;
		$isInstalled = false;//C::IsInstalled();
		
        if (!$isInstalled) {
			C::Show(get_defined_vars(), 'index/install');
			return;
        }
        $admin =  AdminSession::G()->getCurrentAdmin();
        if (!$admin) {
			C::Show(get_defined_vars(), 'account/login');
            return ;
        }		
        C::Show(get_defined_vars(), 'index/index');
    }
	public function dashboard()
	{
		$dashboard=AllInOnBusiness::G()->getDashboard()
		C::Show($dashboard, 'index/dashboard');
	}
}
