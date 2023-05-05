<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Controller;

use DuckAdmin\Business\InstallBusiness;
use DuckAdmin\System\ProjectController;
use DuckAdmin\System\ControllerHelper as C;

/**
 * 主入口
 */
class Install extends Base
{
    /**
     * 设置数据库
     */
    public function step1()
    {
		$user = $request->post('user');
        $password = $request->post('password');
        $database = $request->post('database');
        $host = $request->post('host');
        $port = (int)$request->post('port') ?: 3306;
        $overwrite = $request->post('overwrite');	
		
		try{
			InstallBusiness::G()->step1();
		}catch(\Exception $ex){
			C::ExitJson([$ex->getCode(),$ex->getMessage()]);
		}
		C::ExitJson(0);
    }

    /**
     * 设置管理员

     */
    public function step2()
    {
		try{
			InstallBusiness::G()->step2($username,$password,$password_confirm);
		}catch(\Exception $ex){
			C::ExitJson([$ex->getCode(),$ex->getMessage()]);
		}
		C::ExitJson(0);
    }

}
