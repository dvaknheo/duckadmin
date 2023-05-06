<?php
namespace DuckAdmin\Business;
use DuckAdmin\Model\OptionModel;

/**
 * 个人资料业务
 */
class ConfigBusiness extends BaseBusiness 
{
	public function getDefaultConfig()
	{
		$config = OptionModel::G()->GetSystemConfig(); 
		if (empty($config)) {
			$config = static::Config('system_config',[],'pear_config');
			OptionModel::G()->setSystemConfig($config);
		}
		$config['menu']['data']="/admin/rule/get";
		return $config;
	}
}