<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\System;

/**
 * 这里的集合只是抽调给外部用
 */
class ActionApi
{
	public function id()
	{
		//
	}
	public function isSuper($admin_id)
	{
		//
	}
    public function canAccessPath($path)
    {
        //
    }
	public function canAccessAction($controller,$method)
	{
		//
	}
    public function isMyException(\Exception $ex)
    {
        //
    }
}