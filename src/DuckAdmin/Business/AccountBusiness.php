<?php
namespace DuckAdmin\Business;

/**
 * 个人资料业务
 */
class AccountBusiness extends BaseBusiness 
{
	public function getAccountInfo()
	{
		$data = json_decode(file_get_contents(__DIR__.'/data/account.json'),true);
		return $data;
	}
}