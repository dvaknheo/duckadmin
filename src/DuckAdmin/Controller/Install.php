<?php
namespace plugin\admin\app\controller;

/**
 * 安装
 */
class Install extends Base
{
    /**
     * 设置数据库
     */
    public function step1()
    {
		try{
			InstallBusiness::G()->step1();
		}catch{
			static::ExitJson([$ex->getCode(),$ex->getMessage()]);
		}
		static::ExitJson(0);
    }

    /**
     * 设置管理员

     */
    public function step2()
    {
		try{
			InstallBusiness::G()->step2();
		}catch{
			static::ExitJson([$ex->getCode(),$ex->getMessage()]);
		}
		static::ExitJson(0);
    }

}
