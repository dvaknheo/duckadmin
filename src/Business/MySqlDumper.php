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
        $a=[];
        $data=M::Db()->fetchAll('show tables');
        foreach($data as $v){
            $t=array_values($v);
            $a[]=$t[0];
        }
        $tables =$a;
        
        foreach($tables as $table){
            $records=M::Db()->fetchAll('show create table '.$table);
            $first_names = array_column($records, 'Create Table', 'Table');
            // AUTO_INCREMENT=\d+  // 我们需要把这些东西给清理掉
            
        }
    }
}
