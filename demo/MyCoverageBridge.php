<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */
namespace Demo;

use DuckPhp\Core\App;
use DuckPhp\Core\Console;
use DuckPhp\Core\EventManager;
use DuckPhp\Foundation\Helper;

class MyCoverageBridge extends MyCoverage
{
    //    use SingletonTrait;
    public $options =[
        'path' => '',
        'path_src' => 'src',
        'path_dump' => 'test_coveragedumps',
        'path_report' => 'test_reports',
        'group'=>'',
        'name'=>'',
    ];
    public function __construct()
    {
        parent::__construct();
    }
    public function getRuntimePath()
    {
        $path = static::SlashDir(App::Root()->options['path']);
        $path_runtime = static::SlashDir(App::Root()->options['path_runtime']);
        return static::IsAbsPath($path_runtime) ? $path_runtime : $path.$path_runtime;
    }
    public function init(array $options, ?object $context = null)
    {
        parent::init($options, $context);
        $path = $this->getRuntimePath();
        $this->options['path']=$path;
        $this->options['path_src'] = realpath(__DIR__.'/../').'/src';
        $group = $this->getTestGroup();
        $this->options['group'] = $group; //$this->getTestGroup();
        
        Console::_()->regCommandClass(App::Current()->options['cli_command_prefix']??App::Phase(), App::Phase(), [static::class]);
        EventManager::_()->on([App::Phase(),'onBeforeRun'],[static::class,'OnBeforeRun']);
        EventManager::_()->on([App::Phase(),'onAfterRun'],[static::class,'OnAfterRun']);
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
        
        if(!$this->options['group']){
            return;
        }
        
        $name = $this->getTestName();
        $this->options['name'] = $name;
        
        $path_dump = $this->getSubPath('path_dump');
        @mkdir($path_dump);
        
        ////[[[[
        $data ='';
        $session_id = Helper::COOKIE('PHPSESSID','');
        $post = Helper::POST();
        $post = http_build_query($post);
        if($post){
            $data ="#POST {$post}\n";
        }
        $uri = Helper::SERVER('REQUEST_URI','');
        $data.=$uri ."\n";
        ////]]]]
        file_put_contents($path_dump.$this->options['group'].'.list',$data,FILE_APPEND);
        
        ////////////
        $this->doBegin();
    }
    public function _OnAfterRun()
    {
        if (PHP_SAPI === 'cli' && App::Current()->options['cli_enable']) {
            return;
        }
        ///////////////
        $this->doEnd();
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
        $session_id = Helper::COOKIE('PHPSESSID','');
        $uri = Helper::SERVER('REQUEST_URI','');
        $post = Helper::POST();
        $post = http_build_query($post);
        $ret =implode(";",[$uri,$post,$session_id,$method]);
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
--report [a b c]
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
            $groups = is_array($p['report'])?$p['report']:[];
            $this->createReport($groups);
        }
        
        var_dump(DATE(DATE_ATOM));
        //
    }
    protected $is_save_session=false;
    protected $session_id='';
    protected function replay()
    {
        $this->is_save_session=true;
        $list = file(__DIR__.'/request.list');
        foreach($list as $line){
            $request =trim($line);
            if(!$request){
                continue;
            }

            if(substr($request,0,1)==='#'){
                if(substr($request,0,strlen('#POST '))==='#POST '){
                    parse_str(substr($request,strlen('#POST ')),$post);
                    continue;
                }
                if(substr($request,0,2)==='##'){
                continue;
                }
            }
            $url ="http://admin.demo.local".$request; //TODO
            $data = $this->curl_file_get_contents([$url,'127.0.0.1'],$post);
            $post =[];
        }
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
        if($this->is_save_session){
            curl_setopt($ch, CURLOPT_HEADER, 1);
        }
        
        if(!empty($post)){
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
        }
        if($this->session_id){
            curl_setopt($ch, CURLOPT_COOKIE, "PHPSESSID={$this->session_id}");
        }
        
        
        $data = curl_exec($ch);
        
        if($this->is_save_session){
            $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $headers = substr($data, 0, $header_size);
            $data = substr($data, $header_size);
            
            $flag = preg_match('/PHPSESSID=(\w+)/',$headers,$m);
            if($flag){
                $this->session_id = $m[1];
                $this->is_save_session = false;
            }
        }
        echo $url;
        echo "\n";
        //echo $data;
        curl_close($ch);
        return $data !== false?$data:'';
    }
}