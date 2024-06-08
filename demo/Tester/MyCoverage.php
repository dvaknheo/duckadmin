<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */
namespace DuckAdminDemo\Tester;

use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Report\Html\Facade as ReportOfHtmlOfFacade;
use SebastianBergmann\CodeCoverage\Report\PHP as ReportOfPHP;

class MyCoverage
{
    protected $coverage;

    public $options = [
        'path' => '',
        'path_src' => 'src/',
        'path_dump' => 'test_coveragedumps',
        'path_report' => 'test_reports',
        'group'=>'',
        'name'=>'',
        'test_on_report'=>null,
    ];
    public $is_inited = false;
    
    protected static $_instances = [];
    //embed
    public static function _($object = null)
    {
        if (defined('__SINGLETONEX_REPALACER')) {
            $callback = __SINGLETONEX_REPALACER;
            return ($callback)(static::class, $object);
        }
        if ($object) {
            self::$_instances[static::class] = $object;
            return $object;
        }
        $me = self::$_instances[static::class] ?? null;
        if (null === $me) {
            $me = new static();
            self::$_instances[static::class] = $me;
        }
        
        return $me;
    }
    public function __construct()
    {
    }
    public static function Begin()
    {
        return static::_()->doBegin();
    }
    public static function End()
    {
        return static::_()->doEnd();
    }
    protected static function IsAbsPath($path)
    {
        if (DIRECTORY_SEPARATOR === '/') {
            //Linux
            if (substr($path, 0, 1) === '/') {
                return true;
            }
        } else { // @codeCoverageIgnoreStart
            // Windows
            if (preg_match('/^(([a-zA-Z]+:(\\|\/\/?))|\\\\|\/\/)/', $path)) {
                return true;
            }
        }   // @codeCoverageIgnoreEnd
        return false;
    }
    protected static function SlashDir($path)
    {
        $path = ($path !== '') ? rtrim($path, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR : '';
        return $path;
    }
    protected function getSubPath($path_key)
    {
        if (static::IsAbsPath($this->options[$path_key])) {
            return static::SlashDir($this->options[$path_key]);
        } else {
            return static::SlashDir($this->options['path']) . static::SlashDir($this->options[$path_key]);
        }
    }
    public function init(array $options, ?object $context = null)
    {
        $this->options = array_intersect_key(array_replace_recursive($this->options, $options) ?? [], $this->options);
        
        $this->coverage = new CodeCoverage();
        $this->is_inited = true;
        // auto start
        return $this;
    }
    public function doBegin()
    {
        $path_src = $this->getSubPath('path_src');
        $this->coverage->filter()->addDirectoryToWhitelist($path_src);
        
        $this->coverage->start($this->options['name'],true);
    }
    
    public function doEnd()
    {
        $this->coverage->stop();
        $path_dump = $this->getSubPath('path_dump');
        $path_dump = $path_dump. $this->options['group'].'/';
        
        $file = md5($this->options['name']);
        
        (new ReportOfPHP)->process($this->coverage, $path_dump.$file.'.php');
        //$this->coverage = null;
    }
    public function getCoverage()
    {
        return $this->coverage;
    }
    public function createReport($groups =[])
    {
        $path_src = $this->getSubPath('path_src');
        $path_dump = $this->getSubPath('path_dump');
        $path_report = $this->getSubPath('path_report');
        
        
        if(empty($groups)){
            $groups =[$this->options['group']];
        }
        if(!$this->options['test_report_direct']){
            if(count($groups)===1){
                $path_report = $path_report. $groups[0];
            }else{
                $path_report = $path_report. DATE('y-m-d');
            }
        }
        
        $coverage = new CodeCoverage();
        $coverage->filter()->addDirectoryToWhitelist($path_src);
        $coverage->setTests([
          'T' => [
            'size' => 'unknown',
            'status' => -1,
          ],
        ]);
        
        foreach($groups as $group) {
            $current_path_dump = $path_dump. $group;
            $directory = new \RecursiveDirectoryIterator($current_path_dump, \FilesystemIterator::CURRENT_AS_PATHNAME | \FilesystemIterator::SKIP_DOTS);

            $iterator = new \RecursiveIteratorIterator($directory);
            $files = \iterator_to_array($iterator, false);
            foreach ($files as $file) {
                // 要重复两遍才能 100% ，所以 ignore 得了，使用 include 会导致一个 Bug 。
                $t = static::include_file($file);    //@codeCoverageIgnore
                $coverage->merge($t);   //@codeCoverageIgnore
            }
        }
        $this->coverage = $coverage;
        $this->onBeforeReport();
        (new ReportOfHtmlOfFacade)->process($this->coverage, $path_report);
        $report = $this->coverage->getReport();
        $lines_tested = $report->getNumExecutedLines();
        $lines_total = $report->getNumExecutableLines();
        $lines_percent = sprintf('%0.2f%%', $lines_tested / $lines_total * 100);
        
        $this->coverage = null; 
        return [
            'lines_tested' => $lines_tested,
            'lines_total' => $lines_total,
            'lines_percent' => $lines_percent,
        ];
    }
    protected function onBeforeReport()
    {
        //
    }
    protected static function include_file($file)
    {
        return include $file;
    }
    ////[[[[
    protected function watchingBegin($name)
    {
        file_put_contents($this->options['path'].'MyCoverage.watching.txt',$name);
    }
    protected function watchingEnd()
    {
        @unlink($this->options['path'].'MyCoverage.watching.txt');
    }
    protected function watchingGetName()
    {
        $group = @file_get_contents($this->options['path'].'MyCoverage.watching.txt');
        return $group;    
    }
    ////]]]]
}