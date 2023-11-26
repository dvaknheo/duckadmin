<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Controller;

use DuckPhp\Helper\ControllerHelperTrait;

use DuckAdmin\Business\AccountBusiness;
use DuckAdmin\Controller\AdminSession;

class Helper
{
    use ControllerHelperTrait;
    
    public static function AdminId()
    {
        return AdminSession::_()->getCurrentAdminId();
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
    public static function ThrowOn($flag,$message,$code =-100)
    {
        return ControllerException::ThrowOn($flag,$message,$code);
    }
}