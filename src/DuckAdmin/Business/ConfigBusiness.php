<?php
namespace DuckAdmin\Business;

/**
 * 个人资料业务
 */
class ConfigBusiness extends BaseBusiness 
{
	public function getDefaultConfig()
	{
		$config = OptionModel::G()->GetSystemConfig(); 
		if (empty($config)) {
			$config = static::Config('pear_config');
			OptionModel::G()->setSystemConfig($config);
		}
		return $config;
	}
}