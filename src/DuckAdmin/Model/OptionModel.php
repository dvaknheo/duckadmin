<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Model;
/**
 * 菜单模型
 */
class OptionModel extends Base
{
	protected $table_name = 'options';
    
    public function getSystemConfig()
    {
		$data = $this->fetch("select * from `'TABLE'` where name='system_config'");
		return $data?json_decode($data['value'],true):[];
    }
	public function setSystemConfig($value)
	{
        $data = $this->fetch("select * from `'TABLE'` where name='system_config'");
        if(!$data){
            $flag = $this->execute("insert into `'TABLE'` ( value ) values( ? )" ,json_encode($value));
        }else{
            $flag = $this->execute("update `'TABLE'` set value = ? where name='system_config'" ,json_encode($value));
        }
        return $flag;
	}
}