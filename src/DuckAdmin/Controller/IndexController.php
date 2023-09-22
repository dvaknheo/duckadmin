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
class IndexController extends Base
{
    /**
     * 无需登录的方法
     * @var string[]
     */
    protected $noNeedLogin = ['dashboard'];

    /**
     * 不需要鉴权的方法
     * @var string[]
     */
    protected $noNeedAuth = ['dashboard'];


	public function dashboard()
	{
		var_dump(DATE(DATE_ATOM));return;
		$dashboard=[];//AllInOnBusiness::G()->getDashboard();
		return Helper::Show($dashboard, 'index/dashboard');
	}
}
