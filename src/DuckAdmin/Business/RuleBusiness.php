<?php
namespace DuckAdmin\Business;

/**
 * 个人资料业务
 */
class RuleBusiness extends BaseBusiness 
{
	public function get()
	{
		$data = json_decode(file_get_contents(__DIR__.'/data/rule.json'),true);
		return $data;
	}
}