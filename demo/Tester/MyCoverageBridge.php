<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */
namespace Demo\Tester;

use DuckPhp\Core\App;
use DuckPhp\Core\Console;
use DuckPhp\Foundation\Helper;
use DuckPhp\HttpServer\HttpServer;

class MyCoverageBridge extends MyCoverage
{
    //todo use  global singletonex to replace default singleton function
    public $options =[
        'test_save_web_request_list' =>true,
        'test_save_local_call_list' =>false,
        'test_server_port'=> 8080,
        'test_server_host'=> '',
        'test_path_server'=>'',
        'test_path_document'=>'public',
        'test_homepage' =>'/index_dev.php/',
        
        'test_new_server'=>true,
        'test_list_callback'=>null,
        'test_report_direct'=>true,
        'test_echo_back'=>false,
        
    ];
    protected $is_save_session=false;
    protected $session_id='';
    protected $post =[];
    public function __construct()
    {
        $this->options = array_replace_recursive($this->options, (new parent())->options); //merge parent's options;
        parent::__construct();
    }
    public function init(array $options, ?object $context = null)
    {
        parent::init($options, $context);
    
        $this->options['path'] = Helper::PathForRuntime();
        $this->options['test_path_server'] = Helper::PathForProject();
        $this->options['path_src'] = realpath(__DIR__.'/../../').'/src';

        $this->options['group'] = $this->watchingGetName();

        // 这里抽成一个方法
        $prefix = App::Current()->options['cli_command_prefix']??App::Phase();
        $prefix = App::IsRoot()?'':$prefix;
        $classes = App::Current()->options['cli_command_classes'];
        $classes[] = static::class;
        //get_class(static::_());
        Console::_()->regCommandClass($prefix, App::Phase(), $classes);
        //App::_()->regExtClass(static::class);
        
        Helper::OnEvent([App::Phase(),'onBeforeRun'],[static::class,'OnBeforeRun']);
        Helper::OnEvent([App::Phase(),'onAfterRun'],[static::class,'OnAfterRun']);
        \DuckPhp\Core\ExitException::Init(); //__define(__ExitException);
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
        if($this->options['test_save_web_request_list'] ?? false){
            $path_dump = $this->getSubPath('path_dump');
            @mkdir($path_dump);
            file_put_contents($path_dump.$this->options['group'].'.list',$this->getHttpStringToLog()."\n",FILE_APPEND); 
        }
        
        $watching_name = $this->watchingGetName();
        
        if($watching_name !== Helper::SERVER('HTTP_X_MYCOVERAGE_NAME','')) {
            return;
        }
        $this->options['name'] = $this->getTestName();       
        //// save list


        $before_run = Helper::SERVER('HTTP_X_MYCOVERAGE_BEFORERUN','');
        ////////////
        
        $this->doBegin();
    }
    public function getCoverage()
    {
        return $this->coverage;
    }
    
    public function _OnAfterRun()
    {
        if (PHP_SAPI === 'cli' && App::Current()->options['cli_enable']) {
            return;
        }
        ///////////////
        $watching_name = $this->watchingGetName();
        if($watching_name !== Helper::SERVER('HTTP_X_MYCOVERAGE_NAME','')) {
            return;
        }
        $this->doEnd();
    }
    protected function getHttpStringToLog()
    {
        $data ='';
        $post = Helper::POST();
        $post = http_build_query($post);
        
        $uri = Helper::SERVER('REQUEST_URI','');
        $data .= $uri;
        
        if($post){
            $data .=" {$post}";
        }
        $data .="\n";
        return $data;
    }
    protected function getTestName()
    {
        $time = date('ymdHis.',$_SERVER['REQUEST_TIME']).sprintf('%03d',($_SERVER['REQUEST_TIME_FLOAT']-(int)$_SERVER['REQUEST_TIME_FLOAT'])*1000);

        $method = Helper::SERVER('REQUEST_METHOD','GET');
        
        $session_id = Helper::COOKIE('PHPSESSID','');
        $uri = Helper::SERVER('REQUEST_URI','');
        $post = Helper::POST();
        $post = http_build_query($post);
        $ajax = Helper::IsAjax()?'AJAX':'';
        $ret =implode(";",[$time,$uri,$post,$session_id,$method,$ajax]);
        return $ret;
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
    
    //////////////////
    protected function replay()
    {
        $callback = $this->options['test_list_callback'];
        $test_list = \call_user_func($callback);
        $test_list = explode("\n",$test_list);
        
        $this->is_save_session = true;
        $this->session_id = '';
        $this->post = [];
        
        $this->doBegin();
        
        foreach($test_list as $line){
            $request =trim($line);
            if(!$request){
                continue;
            }
            if(substr($request,0,1)==='#'){
                $this->readCommand($request);
                continue;
            }
        }
        $this->stopServer();
        $this->doEnd();
    }
    protected function readCommand($request)
    {
        if(substr($request,0,2)==='##'){
            return;
        }
        if(substr($request,0,strlen('#CALL '))==='#CALL '){
            $this->explainCall($request);
            // 我们这里写入命令，调用方法， Phase 要怎么处理呢？
        }
        if(substr($request,0,strlen('#WEB '))==='#WEB '){
            $this->explainWeb($request);
        }
    }

    public function prepareCurl($ch)
    {
        return $ch;
    }
    protected function curl_file_get_contents($url, $post =[],$is_ajax = flase,$is_options =false)
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
        $headers =[];
        $headers[] = 'X-MyCoverage-Name: '.$this->watchingGetName();
        $headers[] = 'X-MyCoverage-BeforeRun: ';
        if($is_ajax){
            $headers[] = 'X-Requested-With: XMLHttpRequest';
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        if($is_options){
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'OPTIONS');

        }
        if($this->session_id){
            curl_setopt($ch, CURLOPT_COOKIE, "PHPSESSID={$this->session_id}"); 
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
        echo $url; echo ' ' ; echo http_build_query($post);
        echo "\n";
        //echo $data;
        curl_close($ch);
        $data = ($data !== false)?$data:'';
        return $data;
    }
    ////[[[[
    protected $is_server_started=false;
    
    protected function startServer()
    {
        if($this->is_server_started){
            return;
        }
        $server_options=[
            'path'=> $this->options['test_path_server'],
            'path_document'=>$this->options['test_path_document'],
            'port'=>$this->options['test_server_port'],
            'background' =>true,
            'http_app_class' =>get_class(App::Root()),
        ];
        
        if($this->options['test_new_server']){
            HttpServer::_(new HttpServer()); 
        }
        HttpServer::RunQuickly($server_options);
        
        sleep(1);// ugly
        echo static::class." HTTP SERVER PID = ".HttpServer::_()->getPid() ."\n";
        $this->is_server_started=true;
    }
    protected function stopServer()
    {
        if(!$this->is_server_started){ return;}
        HttpServer::_()->close();
        $this->is_server_started=false;
    }
    ////]]]]
    //////////
    protected function explainWeb($request)
    {
        @list($command,$uri,$poststr,$method, $session_id) = explode(' ',$request);
        
        if($command!=='#WEB'){return;}
        
        $this->startServer();
        $post =[];
        if($poststr){
            parse_str($poststr,$post);
        }
        $is_ajax = ($method ==='AJAX')?true:false;
        $is_options = ($method ==='OPTIONS')?true:false;
        $url ="http://127.0.0.1:{$this->options['test_server_port']}".$this->options['test_homepage'].$uri;
        $data = $this->curl_file_get_contents([$url,'127.0.0.1'],$post,$is_ajax,$is_options);
        if($this->options['test_echo_back']??false){
            echo substr($data,0,200);
        }
    }
    protected function explainCall($request)
    {
        //$flag = preg_match('/#CALL\s+([^@]+)@(\w+)/',$command,$m);
        //if(!$flag){ return; }
        @list($command,$func,$poststr)=explode(' ',$request);
        if($command!=='#CALL'){return;}
        
        
        $this->options['name'] = $command;
        
        ////[[[[
        //// save list
        $path_dump = $this->getSubPath('path_dump');
        @mkdir($path_dump);
        file_put_contents($path_dump.$this->options['group'].'.list',$request ."\n",FILE_APPEND); 
        ////]]]]
        
        call_user_func($func); // TODO a=1&b=2 ... // 还是要拆分 ：： @ ,要反射
        
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
        $p = Console::_()->getCliParameters();
        if($p['watch']??false){
            if($p['watch']===true){
                $p['watch'] = DATE('Y_m_d_H_i_s');
            }
            $this->watchingBegin($p['watch']);
            echo "watching {$p['watch']}\n";
        }
        if($p['stop']??false){
            $this->watchingEnd();
        }
        if($p['replay']??false){
            $this->replay();
        }
        
        if($p['report']??false){
            echo "reporting...\n";
            $groups = is_array($p['report'])?$p['report']:[];
            $this->createReport($groups);
        }
        
        var_dump(DATE(DATE_ATOM));
    }
    ////]]]]
}