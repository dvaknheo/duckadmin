<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */
namespace Demo\Test;

use DuckPhp\Core\App;
use DuckPhp\Core\Console;
use DuckPhp\Core\PhaseContainer;
use DuckPhp\Foundation\Helper;
use DuckPhp\HttpServer\HttpServer;

class MyCoverageBridge extends MyCoverage
{
    public $options =[
        'test_server_port'=> 8080,
        'test_server_host'=> '',
        'test_homepage' =>'/index_dev.php/',
        'test_path_document'=>'public',
        'test_new_server'=>true,
        //'test_list_callback'=>''
    ];
    protected $is_save_session=false;
    protected $session_id='';
    public function __construct()
    {
        $this->options = array_replace_recursive($this->options, (new parent())->options); //merge parent's options;
        parent::__construct();
    }
    public function init(array $options, ?object $context = null)
    {

        parent::init($options, $context);
        $path = App::PathForRuntime();

        $this->options['path'] = $path;
        $this->options['path_src'] = realpath(__DIR__.'/../../').'/src';

        $this->options['group'] = $this->watchingGetName();

        $prefix = App::Current()->options['cli_command_prefix']??App::Phase();
        $prefix = App::IsRoot()?'':$prefix;
        Console::_()->regCommandClass($prefix, App::Phase(), [static::class]);
        App::OnEvent([App::Phase(),'onBeforeRun'],[static::class,'OnBeforeRun']);
        App::OnEvent([App::Phase(),'onAfterRun'],[static::class,'OnAfterRun']);
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
        $data =$this->getHttpStringToLog();
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
    protected function getHttpStringToLog()
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
        $path = App::PathForRuntime();
        file_put_contents($path.'MyCoverage.watching.txt',$name);
    }
    protected function watchingEnd()
    {
        $path = App::PathForRuntime();
        @unlink($path.'MyCoverage.watching.txt');
    }
    protected function watchingGetName()
    {
        $path = App::PathForRuntime();
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
    //////////////////
    
    private $phase_map =[];
    private function get_all_namepace_phase_map()
    {
        $classes = PhaseContainer::GetContainerInstanceEx()->publics;
        $phase_map =[];
        foreach($classes as $class =>$_){
            if(!isset($class::_()->options['namespace'])){continue;}
            $ret[$class::_()->options['namespace'].'\\' ]= $class;
        }
        return $phase_map;
    }
    private function phase_from_class($class)
    {
        if(!$this->phase_map){
            $this->phase_map = $this->get_all_namepace_phase_map();
        }
        foreach ($this->phase_map as $k=>$v) {
            if(substr($class,0,strlen($k))===$k){
                return $v;
            }
        }
        return '';
    }

    protected $post =[];
    protected function replay()
    {
        $this->startServer();
        $test_list = $this->getTestList();
        $this->post =[];
        foreach($test_list as $line){
            $request =trim($line);
            if(!$request){
                continue;
            }

            if(substr($request,0,1)==='#'){
                $this->readCommand($request);
                continue;
            }

            $url ="http://127.0.0.1:{$this->options['test_server_port']}".$this->options['test_homepage'].$request;
            $data = $this->curl_file_get_contents([$url,'127.0.0.1'],$this->post);
            if($this->options['test_echo_back']??false){
                echo substr($data,0,100);
            }
            $this->post =[];
        }
        $this->stopServer();
    }
    protected function readCommand($request)
    {
        if(substr($request,0,strlen('#POST '))==='#POST '){
            $this->explainPost($request);
            return;
        }
        if(substr($request,0,2)==='##'){
            return;
        }
        if(substr($request,0,strlen('#CALL '))==='#CALL '){
            $this->explainCall($request);
            // 我们这里写入命令，调用方法， Phase 要怎么处理呢？
        }
        if(substr($request,0,strlen('#COMMAND '))==='#COMMAND '){
            // 我们这里写入命令，调用方法， Phase 要怎么处理呢？
        }
    }
    protected function getTestList()
    {
        $routes_text = \DuckAdmin\Test\RunAll::_()->getAllRouteToRun();
        $prefix=\DuckAdmin\System\DuckAdminApp::_()->options['controller_url_prefix'];
        $routes_text = str_replace('#WEB ',$prefix,$routes_text);
        return explode("\n",$routes_text);
    }
    public function prepareCurl($ch)
    {
        return $ch;
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
        $this->prepareCurl($ch);
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
        $data = ($data !== false)?$data:'';
        return $data;
    }
    protected function startServer()
    {
        $server_path = App::PathForProject();
        $server_options=[
            'path'=>$server_path,
            'path_document'=>$this->options['test_path_document'],
            'port'=>$this->options['test_server_port'],
        ];
        $server_options['background'] =true;
        if($this->options['test_new_server']){
            HttpServer::_(new HttpServer()); 
        }
        HttpServer::RunQuickly($server_options);
        //echo HttpServer::_()->getPid();
        sleep(1);// ugly
    }
    protected function stopServer()
    {
        HttpServer::_()->close();
    }
    //////////

    protected function explainPost($request)
    {
        parse_str(substr($request,strlen('#POST ')),$post);
        $this->post = $post;
    }
    protected function explainCall($command)
    {
        $flag = preg_match('/#CALL\s+([^@]+)@(\w+)/',$command,$m);
        if(!$flag){ return; }
        list($command,$class,$method) = $m;
        $phase = $this->phase_from_class($class);
        if(!$phase){ return; }
        
        // save name ;
        $this->options['name'] = $command;
        $last_phase = App::Phase();
        $class::_()->$method();
        App::Phase($last_phase);

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
        $path = App::PathForRuntime();
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
    public function command_testgroup2()
    {
        $command = '#CALL DuckAdmin\Test\RunAll@test';
        $this->explain_call($command);
        var_dump(DATE(DATE_ATOM));
    }
    public function command_gentest()
    {
        $this->startServer();
        echo "waiting...\n";
      
        $request ='app/admin/index';
        //$data = $this->curl_file_get_contents("http://127.0.0.1:{$this->options['test_server_port']}".$this->options['test_homepage'],$this->post);
        $this->curl_file_get_contents(["http://admin.demo.local".$this->options['test_homepage'],'127.0.0.1:8080'],$this->post);
        var_dump($data);
        
        $this->stopServer();
        var_dump(DATE(DATE_ATOM));return;
        $x=App::Root()->options;
        $last_phase = App::Phase(\DuckAdmin\System\DuckAdminApp::class);
        //$routes = TestFileCreator::_()->genRunFile();
        App::Phase($last_phase);
        var_dump(DATE(DATE_ATOM));
    }
}