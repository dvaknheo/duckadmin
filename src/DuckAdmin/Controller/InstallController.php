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
		$post['user'] = Helper::POST('user');
        $post['password'] = Helper::POST('password');
        $post['database'] = Helper::POST('database');
        $post['host'] = Helper::POST('host');
        $post['port'] = (int)Helper::POST('port') ?: 3306;
        $post['overwrite'] = Helper::POST('overwrite');	
		
		try{
			InstallBusiness::G()->step1($post);
		}catch(\Exception $ex){
			return Helper::ExitJson(['code' => $ex->getCode(), 'msg' => $ex->getMessage(), 'type' => 'error']);
		}
		return Helper::ExitJson(['code' =>0, 'data' => [] , 'msg' => '',]);
    }

    /**
     * 设置管理员

     */
    public function step2()
    {
		$username = Helper::POST('username');
        $password = Helper::POST('password');
        $password_confirm = Helper::POST('password_confirm');
		try{
			InstallBusiness::G()->step2($username,$password,$password_confirm);
		}catch(\Exception $ex){
			return Helper::ExitJson(['code' => $ex->getCode(), 'msg' => $ex->getMessage(), 'type' => 'error']);
		}
		return Helper::ExitJson(['code' =>0, 'data' => [] , 'msg' => '',]);
    }

}
