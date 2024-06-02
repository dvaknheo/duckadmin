<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */
namespace Demo\Test;

use Demo\Tester\MyCoverageBridge;
use DuckPhp\Component\ExtOptionsLoader;
use DuckPhp\Component\DbManager;
use DuckPhp\Core\Console;
use DuckPhp\Core\App;
use DuckPhp\DuckPhp;
use DuckPhp\Foundation\SimpleSingletonTrait;
use DuckPhp\Foundation\Helper;
use DuckPhp\FastInstaller\FastInstaller;
//use DuckPhp\Foundation\System\Helper;

class MyTester
{
    use SimpleSingletonTrait;

    public static function BeforeWebTest()
    {
        return static::_()->_BeforeWebTest();
    }
    public static function AfterWebTest()
    {
        return static::_()->_AfterWebTest();
    }
    public static function GetTestList()
    {
        return static::_()->_GetTestList();
    }
    public static function BeforeReplayTest()
    {
        return static::_()->_BeforeReplayTest();
    }
    public static function AfterReplayTest()
    {
        return static::_()->_AfterReplayTest();
    }
    public static function OnReport()
    {
        return static::_()->_OnReport();
    }
    public function installTest()
    {
        $this->cleanAll();
        $db_file = 'db_fortest.db';
        $input = <<<EOT
{$db_file}
n
admin
123456
123456

EOT;
        Console::_()->readLinesFill($input);
        FastInstaller::_()->doInstall(); // App::_()->callConsole('install');
        var_dump(DATE(DATE_ATOM));
    }
    public function cleanAll()
    {
        @unlink(App::_()->options['ext_options_file']);
        App::_()->options['ext_options_file_enable']=true;
        $db_file = 'db_fortest.db';
        @unlink(Helper::PathOfRuntime().$db_file);
    }
    public function _GetTestList()
    {
        
        $str ='';
        //$str .= \DuckAdmin\Test\Tester::_()->getTestList();
        $str .= \DuckUser\Test\Tester::_()->getTestList();
        //$str .= \SimpleBlog\Test\Tester::_()->getTestList();
        //$str .= \DuckUserManager\Test\Tester::_()->getTestList();
        
        //path = \DuckAdmin\DuckAdminApp::_()->options['path'];
        //$filter = MyCoverageBridge::_()->getCoverage()->filter();
        //$filter->removeDirectoryFromWhitelist($path.'Test');
        //$filter->removeDirectoryFromWhitelist($path.'View');
        //    public function removeDirectoryFromWhitelist(string $directory, string $suffix = '.php', string $prefix = ''): void
        //    public function removeFileFromWhitelist(string $filename): void

        return $str;
    }
    public function _BeforeWebTest()
    {
    }
    public function _AfterWebTest()
    {
        //
    }
    public function _BeforeReplayTest()
    {
        $this->installTest();
    }
    public function _AfterReplayTest()
    {
        $this->cleanAll();
    }
    protected function in_paths($paths,$file)
    {
        foreach($paths as $v){
            if($v === substr($file,0,strlen($v))){
                return true;
            }
        }
        return false;
    }
    public function _OnReport()
    {
        return;
        $coverage = MyCoverageBridge::_()->getCoverage();
        $path = realpath(\DuckUser\System\DuckUserApp::_()->options['path']).'/';
        
        $paths[]=$path.'Test/';
        //$paths[]=$path.'view/';
        //$paths[]=$path.'Controller/';
        
        $data = $coverage->getData();
        $new_data = [];
        foreach($data as $file =>$v){
            if($this->in_paths($paths,$file)){
                continue;
            }
            $new_data[$file] = $v;
        }
        $filter = MyCoverageBridge::_()->getCoverage()->filter();
        $filter->removeDirectoryFromWhitelist($path.'Test/');
        //$filter->removeDirectoryFromWhitelist($path.'View');
        $coverage->setData($data);
        $filter = MyCoverageBridge::_()->getCoverage()->filter();
        $filter->removeDirectoryFromWhitelist($path.'Test/');
        return;
        //$coverage = MyCoverageBridge::_()->getCoverage();
        
        $path = realpath(\DuckUser\System\DuckUserApp::_()->options['path']).'/';
        //var_dump($path);
    }
}