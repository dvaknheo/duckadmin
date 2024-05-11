<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */
namespace Demo;

use DuckPhp\Core\App;
use DuckPhp\Core\ComponentBase;
use DuckPhp\Core\EventManager;
use DuckPhp\Foundation\Helper;
class MyCoverageBridge extends ComponentBase
{
    //    use SingletonTrait;
    public $options =[
        'log_request'=>false,
        'path_src' => null,
    ];
    ////
    public function getRuntimePath()
    {
        //TODO to helper ,PathOfRuntime
        $path = static::SlashDir(App::Root()->options['path']);
        $path_runtime = static::SlashDir(App::Root()->options['path_runtime']);
        return static::IsAbsPath($path_runtime) ? $path_runtime : $path.$path_runtime;
    }
    protected function initOptions(array $options)
    {
        
        $path = $this->getRuntimePath();
        
        $group = App::Setting('group',null);
        $group = $group ?? date('H_i_s');
        
        
        MyCoverage::_()->options['path']=$path;
        MyCoverage::_()->options['group']=$group;
        
        MyCoverage::_()->init($options);
        
        EventManager::OnEvent([get_class(App::Current()),'onBeforeRun'],[static::class,'onBeforeRun']);
        EventManager::OnEvent([get_class(App::Current()),'onAfterRun'],[static::class,'OnAfterRun']);
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
        if($this->options['log_request']){
            $path = MyCoverage::_()->options['path'];
            $group = MyCoverage::_()->options['group'];
            
            file_put_contents($path.$group.'.requests.log',$name."\n",FILE_APPEND);
        }
        ////////////
        MyCoverage::_()->options['name'] = $name;
        MyCoverage::_()->options['path_src']=$this->options['path_src'];
        MyCoverage::Begin();
    }
    public function _OnAfterRun()
    {
        if (PHP_SAPI === 'cli' && App::Current()->options['cli_enable']) {
            return;
        }
        MyCoverage::End();
    }
    public function createReport()
    {
        return MyCoverage::_()->createReport();
    }
    protected function getTestGroup()
    {
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
    public function command_test_group()
    {
        var_dump("hit!");
        //
    }
    public function command_testreport()
    {
        MyCoverageBridge::_()->createReport();
        var_dump("dump report done.");
        var_dump(DATE(DATE_ATOM));
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
    
}