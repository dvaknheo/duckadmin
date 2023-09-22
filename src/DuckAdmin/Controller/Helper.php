<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Controller;

use DuckPhp\Helper\ControllerHelperTrait;
use DuckPhp\SingletonEx\SingletonExTrait;
use DuckPhp\ThrowOn\ThrowOnableTrait;

use DuckAdmin\Business\AccountBusiness;
use DuckAdmin\Controller\AdminSession;

class Helper
{
    public static function AdminId()
    {
        return AdminSession::G()->getCurrentAdminId();
    }
    
    public static function Success($data = [],$count = null)
	{
		if(is_null($count)){
			static::ExitJson(['code' => 0, 'data' => $data, 'msg' => 'ok']);
		}else{
			static::ExitJson(['code' => 0, 'msg' => 'ok', 'count' => $count, 'data' => $data]);
		}
	}
	public static function ShowException($ex)
	{
		$code = $ex->getCode();
		$msg = $ex->getMessage();
		if(!$code){$code = -1;}
        
		return static::ExitJson(['code' => $code, 'msg' => $msg, 'type' => 'error'],false);
	}
}