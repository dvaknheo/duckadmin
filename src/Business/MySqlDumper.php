<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Business;

/**
 * 这里准备作为通用的 sqldumper
 */
class MySqlDumper extends Base
{
    public function foo()
    {
        $tables = $this->getTables();
        
        foreach($tables as $table){
            
            
        }
    }
    protected function getTabes()
    {
        $a=[];
        $data=M::Db()->fetchAll('show tables');
        foreach($data as $v){
            $t=array_values($v);
            $a[]=$t[0];
        }
        return $a;
    }
    public function getCreate($table)
    {
            $records=M::Db()->fetchAll('show create table '.$table);
            $tables = array_column($records, 'Create Table', 'Table');
            var_dump();
            //$str = preg_replace('/AUTO_INCREMENT=\d+/', 'AUTO_INCREMENT=1', $str);
            
    }
    protected function writeSettingFile($setting)
    {
        $this->options['path_config'] = App::G()->options['path_config'] ?? 'config';
        $path = $this->getComponenetPathByKey('path_config');
        $setting_file = $this->options['setting_file'] ?? 'setting';
        $file = $path.$setting_file.'.php';
        
        $data = '<'.'?php ';
        $data .="\n // gen by ".static::class.' '.date(DATE_ATOM) ." \n";
        $data .= ' return ';
        $data .= var_export($setting,true);
        $data .=';';
        return @file_put_contents($file,$data);
    }
}
