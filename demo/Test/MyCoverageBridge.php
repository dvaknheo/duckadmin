<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */
namespace Demo\Test;

use DuckPhp\Core\App;
use DuckPhp\Core\Console;
use DuckPhp\Core\EventManager;
use DuckPhp\Foundation\Helper;
use DuckPhp\Core\PhaseContainer;

class MyCoverageBridge extends MyCoverage
{
    //    use SingletonTrait;
    public $options =[
        'test_server_port'=> '',
    ];
    protected $is_save_session=false;
    protected $session_id='';
    public function __construct()
    {
        $this->options = array_replace_recursive($this->options, (new parent())->options); //merge parent's options;
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
        $this->options['path'] = $path;
        $this->options['path_src'] = realpath(__DIR__.'/../').'/src';
        
        $this->options['group'] = $this->getTestGroup();
        
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
        if(!$this->options['group']){
            return;
        }
        if (PHP_SAPI === 'cli' && App::Current()->options['cli_enable']) {
            //TODO console mode
            return;
        }
        
        $this->options['name'] = $this->getTestName();
        
        //// save list
        $path_dump = $this->getSubPath('path_dump');
        @mkdir($path_dump);
        $data =$this->getCommandToSave();
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
    protected function getCommandToSave()
    {
        $data ='';
        $post = Helper::POST();
        $post = http_build_query($post);
        if($post){
            $data ="#POST {$post}\n";
        }
        $uri = Helper::SERVER('REQUEST_URI','');
        $data.=$uri ."\n";
        return $data;
    }
    protected function watchingBegin($name)
    {
        $path = $this->getRuntimePath();
        file_put_contents($path.'MyCoverage.watching.txt',$name);
    }
    protected function watchingEnd()
    {
        $path = $this->getRuntimePath();
        @unlink($path.'MyCoverage.watching.txt');
    }
    protected function watchingGetName()
    {
        $path = $this->getRuntimePath();
        $group = @file_get_contents($path.'MyCoverage.watching.txt');
        return $group;    
    }
    protected function getTestGroup()
    {
        return $this->watchingGetName();
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
    protected function get_all_namepace_phase_map()
    {
        $classes = PhaseContainer::GetContainerInstanceEx()->publics;
        $ret =[];
        foreach($classes as $class =>$_){
            if(!isset($class::_()->options['namespace'])){continue;}
            $ret[$class::_()->options['namespace'].'\\' ]= $class;
        }
        return $ret;
    }
    protected function phase_from_class($phase_map ,$class)
    {
        foreach ($phase_map as $k=>$v) {
            if(substr($class,0,strlen($k))===$k){
                return $v;
            }
        }
        return '';
    }
    public function command_testgroup2()
    {
        $phase_map = $this->get_all_namepace_phase_map();
        
        $command = '#CALL DuckAdmin\Test\RunAll@test';
        $flag = preg_match('/#CALL\s+([^@]+)@(\w+)/',$command,$m);
        if(!$flag){ return; }
        list($command,$class,$method) = $m;
        
        $phase = $this->phase_from_class($phase_map,$class);
        
        if(!$phase){ return; }
        
        // save name ;
        
        $last_phase = App::Phase();
        $class::_()->$method();
        App::Phase($last_phase);
        var_dump(DATE(DATE_ATOM));
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
            $this->watchingBegin($p['watch']);
            echo "watching {$p['watch']}\n";
            //return;
        }
        if($p['stop']??false){
            $this->watchingEnd();
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

    protected $post =[];
    protected function replay()
    {
        $test_list = $this->getTestList();
        $this->startServer();
        $this->post =[];
        foreach($list as $line){
            $request =trim($line);
            if(!$request){
                continue;
            }

            if(substr($request,0,1)==='#'){
                $this->readCommand($request);
                continue;
            }
            $url ="http://admin.demo.local".$request; //TODO
            $data = $this->curl_file_get_contents([$url,'127.0.0.1'],$post);
            $this->post =[];
        }
        $this->stopServer();
    }
    protected function readCommand($request)
    {
        if(substr($request,0,strlen('#POST '))==='#POST '){
            parse_str(substr($request,strlen('#POST ')),$post);
            return;
        }
        if(substr($request,0,2)==='##'){
            return;
        }
        if(substr($request,0,strlen('#CALL '))==='#CALL '){
            // 我们这里写入命令，调用方法， Phase 要怎么处理呢？
        }
    }
    protected function getTestList()
    {
        $list = file(__DIR__.'/request.list');
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
            curl_setopt($ch, CURLOPT_COOKIE, "PHPSESSID={$this->session_id}"); //TODO
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
    public function startServer()
    {
        $server_path = App::PathForProject();
        $server_options=[
            'path'=>$server_path,
            'path_document'=>'public',
            'port'=>$this->options['test_server_port'],
        ];
        $server_options['background'] =true;
        HttpServer::RunQuickly($server_options);
        //echo HttpServer::_()->getPid();
        sleep(1);// ugly
    }
    public function stopServer()
    {
        HttpServer::_()->close();
    }
}