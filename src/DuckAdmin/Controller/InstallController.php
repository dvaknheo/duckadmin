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
class InstallController extends Base
{
    /**
     * 无需登录的方法
     * @var string[]
     */
    protected $noNeedLogin = ['step1','step2'];
    /**
     * 不需要鉴权的方法
     * @var string[]
     */
    protected $noNeedAuth = ['step1','step2'];
	
    /**
     * 设置数据库
     */
    public function step1()
    {
		$post = [];
		$post['user'] = C::POST('user');
        $post['password'] = C::POST('password');
        $post['database'] = C::POST('database');
        $post['host'] = C::POST('host');
        $post['port'] = (int)C::POST('port') ?: 3306;
        $post['overwrite'] = C::POST('overwrite');	
		
		try{
			InstallBusiness::G()->step1($post);
		}catch(\Exception $ex){
			return C::ExitJson([$ex->getCode(),$ex->getMessage()]);
		}
		return C::ExitJson(0);
    }

    /**
     * 设置管理员

     */
    public function step2()
    {
		$user = C::POST('user');
        $password = C::POST('password');
        $password_confirm = C::POST('password_confirm');
		try{
			InstallBusiness::G()->step2($username,$password,$password_confirm);
		}catch(\Exception $ex){
			return C::ExitJson([$ex->getCode(),$ex->getMessage()]);
		}
		return C::ExitJson(0);
    }

}
