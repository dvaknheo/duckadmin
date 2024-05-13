<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */
namespace Demo;

use DuckPhp\Core\App;
use DuckPhp\Core\ComponentBase;
use DuckPhp\Core\Console;
use DuckPhp\Core\EventManager;
use DuckPhp\Foundation\Helper;

class MyCoverageBridge extends ComponentBase
{
    //    use SingletonTrait;
    public $options =[
        'path_src' => null,
    ];
    ////
    public function getRuntimePath()
    {
        $path = static::SlashDir(App::Root()->options['path']);
        $path_runtime = static::SlashDir(App::Root()->options['path_runtime']);
        return static::IsAbsPath($path_runtime) ? $path_runtime : $path.$path_runtime;
    }
    protected function initOptions(array $options)
    {
        $path = $this->getRuntimePath();
        MyCoverage::_()->options['path']=$path;
        //MyCoverage::_()->options['group']=$group;
        MyCoverage::_()->options['path_src'] = realpath(__DIR__.'/../').'/src';
        MyCoverage::_()->init($options);
        
        Console::_()->regCommandClass(App::Current()->options['cli_command_prefix']??App::Phase(), App::Phase(), [static::class]);

        App::Current()->on([App::Phase(),'onBeforeRun'],[static::class,'OnBeforeRun']);
        App::Current()->on([App::Phase(),'onAfterRun'],[static::class,'OnAfterRun']);
    }
    
    public static function OnBeforeRun()
    {
        return static::_()->_OnBeforeRun();
    }
    public function OnAfterRun()
    {
        return static::_()->_OnAfterRun();
    }
    public function _OnBeforeRun()
    {
        if (PHP_SAPI === 'cli' && App::Current()->options['cli_enable']) {
            return;
        }
        
        $name = $this->getTestName();
        $group = $this->getTestGroup();
        if(!$group){
            return;
        }
        $path = $this->getRuntimePath();
        @mkdir($path.'test_coveragedumps/');
        file_put_contents($path.'test_coveragedumps/'.$group.'.list',$name."\n",FILE_APPEND);
        
        ////////////
        MyCoverage::_()->options['group'] = $group;
        MyCoverage::_()->options['name'] = $name;
        MyCoverage::Begin();
    }
    public function _OnAfterRun()
    {
        if (PHP_SAPI === 'cli' && App::Current()->options['cli_enable']) {
            return;
        }
        ///////////////
        MyCoverage::End();
    }
    public function createReport()
    {
        return MyCoverage::_()->createReport();
    }
    protected function getTestGroup()
    {
        $path = $this->getRuntimePath();
        $group = @file_get_contents($path.'MyCoverage.watching.txt');
        return $group;
    }
    protected function getTestName()
    {
        $method = Helper::SERVER('REQUEST_METHOD','GET');
        $session_id = Helper::session_id();
        $uri = Helper::SERVER('REQUEST_URI','');
        $post = Helper::POST();
        $post = http_build_query($post);
        $ret =implode("\t",[$uri,$post,$session_id,$method]);
        return $ret;
    }
    /**
     * tests group. use --help for more.
     */
    public function command_testgroup()
    {
        $p = Console::_()->getCliParameters();
        if($p['help']??false){
            $str = <<<EOT
--watch {name}
--stop
--replay
--report
EOT;
            echo $str;
            return;
        }
        $path = $this->getRuntimePath();
        $p = Console::_()->getCliParameters();
        if($p['watch']??false){
            if($p['watch']===true){
                $p['watch'] = DATE('Y_m_d_H_i_s');
            }
            file_put_contents($path.'MyCoverage.watching.txt',$p['watch']);
             echo "watching {$p['watch']}\n";
            //return;
        }
        if($p['stop']??false){
            @unlink($path.'MyCoverage.watching.txt');
            //return;
        }
        if($p['replay']??false){
            $this->replay();
        }
        
        if($p['report']??false){
            $group = $this->getTestGroup();
            MyCoverage::_()->options['group'] = $group;
            MyCoverage::_()->createReport();
        }
        
        
        var_dump(DATE(DATE_ATOM));
        //
    }
    
    protected function replay()
    {
        $path = $this->getRuntimePath();
        $group = $this->getTestGroup();
        $list = file($path.'test_coveragedumps/'.$group.'.list');
        var_dump($list);
    }
    protected function curl_file_get_contents($url, $post =[])
    {
        $ch = curl_init();
        
        if (is_array($url)) {
            list($base_url, $real_host) = $url;
            $url = $base_url;
            $host = parse_url($url, PHP_URL_HOST);
            $port = parse_url($url, PHP_URL_PORT);
            $c = $host.':'.$port.':'.$real_host;
            curl_setopt($ch, CURLOPT_CONNECT_TO, [$c]);
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        if(!empty($post)){
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
            //$this->prepare_token();
        }
        
        
        $data = curl_exec($ch);
        curl_close($ch);
        return $data !== false?$data:'';
    }

    
    //////////////////////////
    protected function get_component_path($component)
    {
        $base_class = App::Current()->options['namespace']."\\{$component}\\Base";
        $ref = new \ReflectionClass($base_class);
        $path = dirname($ref->getFilename()).'/';
        return $path;
    }
    protected function get_all_component_classes($path, $component)
    {
        $directory = new \RecursiveDirectoryIterator($path, \FilesystemIterator::CURRENT_AS_PATHNAME | \FilesystemIterator::SKIP_DOTS);
        $iterator = new \RecursiveIteratorIterator($directory);
        $files = \iterator_to_array($iterator, false);
        
        $ret = [];
        foreach ($files as $file) {
            if(substr($file,-strlen($component.'.php'))!==$component.'.php'){continue;};
            $ret[] = $file;
        }
        return $ret;
    }
    public function getBusinessTestString($path, $file, $namespace)
    {
        $data = file_get_contents($file);
        preg_match_all('/public\s+function (([^\(]+)\([^\)]*\))/', (string)$data, $m);
        $funcs = $m[1];
        
        $ret = '';
        $class = substr($file,strlen($path),-strlen('.php'));
        $class = $namespace .str_replace('/','\\',$class);
        foreach ($funcs as $v) {
            $v = str_replace(['&','callable '], ['',''], $v);
            $ret .= "        \\{$class}::_()->$v;\n";
        }
        return $ret;
    }
    public function getAllComponentTestTemplate($component)
    {
        $path = $this->get_component_path($component);
        $files = $this->get_all_component_classes($path,$component);
        $namespace = App::Current()->options['namespace']."\\{$component}\\";
        $data =[];
        foreach($files as $file){
            $data[]= $this->getBusinessTestString($path,$file,$namespace);
        }
        return implode("\n",$data)."\n";
    }
    public function command_foo2()
    {
        $last_phase = App::Phase(\DuckAdmin\System\DuckAdminApp::class);
        $str = $this->getAllComponentTestTemplate('Controller');
        echo $str;
        App::Phase($last_phase);
    }
}