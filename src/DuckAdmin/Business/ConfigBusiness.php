<?php
namespace DuckAdmin\Business;

/**
 * 个人资料业务
 */
class ConfigBusiness extends BaseBusiness 
{
	public function getDefaultConfig()
	{
		$data = json_decode(file_get_contents(__DIR__.'/data/config.json'),true);
		return $data;
		
		$name = 'system_config';
		$config = OptionModel::G()->GetSystemConfig(); 
		if (empty($config)) {
			$config = file_get_contents(base_path('plugin/admin/public/config/pear.config.json'));
			
			OptionModel::G()->setSystemConfig($config);
		}
		return $config;
	}
}