<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Model;
/**
 * 菜单模型
 */
class OptionModel extends BaseModel
{
    public function GetSystemConfig()
    {
		//where('name', $name)->value('value');
		return self::Db()->fetch("select * from wa_options where name='system_config'");
    }
	public function setSystemConfig($value)
	{
		//
	}
}