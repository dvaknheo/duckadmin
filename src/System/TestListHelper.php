<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */
namespace DuckAdmin\System;

use DuckCoverage\DuckCoverage;
use DuckPhp\Component\DbManager;
use DuckPhp\Core\App;
use DuckPhp\Core\SingletonExTrait;

class TestListHelper
{
    use SingletonExTrait;

    public function getTestDbFile(string $name)
    {
        $flag = DuckCoverage::_()->getFlag();
        $dbfile = 'runtime/DuckCoverage/' . DuckCoverage::_()->watchingGetName();
        $dbfile .= $name ? '-' . $name . '.db' : '.db';
        return $dbfile;
    }
    public function cleanInstall()
    {
        $json_file = App::_()->getRuntimePath() . DuckCoverage::_()->options['duckcoverage_data_file_json_file'];
        if(is_file($json_file)){
            $data = json_decode(file_get_contents($json_file), true);
            $dsn = $data[App::Phase()]['database_list'][0]['dsn'] ?? null;
            unset($data[App::Phase()]);
            $string = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
            file_put_contents($json_file, $string);
            if($dsn){
                @list($driver,$dbfile) = explode(":",$dsn);
                @unlink($dbfile);

            }
            clearstatcache();
            echo "install cleaned\n";
        }
        
    }
    public function loadDb()
    {
        $json_file = App::_()->getRuntimePath() . DuckCoverage::_()->options['duckcoverage_data_file_json_file'];
        $data = json_decode(file_get_contents($json_file), true);
        $data = $data[App::Phase()];
        DbManager::_()->init($data);
    }
}