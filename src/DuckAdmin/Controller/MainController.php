<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Controller;

use DuckAdmin\System\ProjectController;
use DuckAdmin\System\ControllerHelper as C;

/**
 * 主入口
 */
class MainController extends ProjectController
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
		C::Show([], 'index/index');return;
		$isInstalled = false;//C::IsInstalled();
		
        if (!$isInstalled) {
			C::Show([], 'index/install');
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
		var_dump(DATE(DATE_ATOM));return;
		$dashboard=[];//AllInOnBusiness::G()->getDashboard();
		C::Show($dashboard, 'index/dashboard');
	}
}
