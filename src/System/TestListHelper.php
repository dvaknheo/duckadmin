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
    /**
     * 应用运行数据文件 —— 框架 ExtOptionsLoader 用的就是它（getRuntimePath() + data_file_json_file），
     * 内容按 phase 分键（根应用是 ""，子应用是 ":DuckUser" 这类）。
     *
     * 坑：不要读 duckcoverage_data_file_json_file —— 那是采集器自己的数据文件，
     * 里面只有 ""（根 phase）一格，按子应用 phase 取必然 Undefined array key。
     */
    protected function dataFile(): string
    {
        $name = (string)(App::_()->options['data_file_json_file'] ?? 'DuckPhpData.config.json');
        return App::_()->getRuntimePath() . $name;
    }
    public function cleanInstall()
    {
        $json_file = $this->dataFile();
        if (!is_file($json_file)) {
            return;
        }
        $all = json_decode((string)file_get_contents($json_file), true);
        $all = is_array($all) ? $all : [];
        $phase = App::_()->getThisPhaseName();
        $dsn = $all[$phase]['database_list'][0]['dsn'] ?? null;
        unset($all[$phase]);
        file_put_contents($json_file, json_encode($all, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK));
        if ($dsn) {
            @list($driver, $dbfile) = explode(':', $dsn);
            @unlink($dbfile);
        }
        clearstatcache();
        echo "install cleaned\n";
    }
    public function loadDb()
    {
        $json_file = $this->dataFile();
        $all = is_file($json_file) ? json_decode((string)file_get_contents($json_file), true) : [];
        $all = is_array($all) ? $all : [];
        $data = (array)($all[App::_()->getThisPhaseName()] ?? []);
        if ($data) {
            DbManager::_()->init($data);
        }
    }
}