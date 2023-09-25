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
		$data = static::Db()->fetch("select * from wa_options where name='system_config'");
		return $data?json_decode($data['value'],true):[];
    }
	public function setSystemConfig($value)
	{
		return static::Db()->execute("update wa_options  set value =? where name='system_config'" ,json_encode($value));
	}
}