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
        'test_before_replay'=>null,
        'test_after_replay'=>null,
        'test_report_direct'=>true,
        'test_echo_back'=>false,
        'test_on_report'=>null,
        
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
    
        $this->options['path'] = Helper::PathOfRuntime();
        $this->options['test_path_server'] = Helper::PathOfProject();
        $this->options['path_src'] = realpath(__DIR__.'/../../').'/src';

        $this->options['group'] = $this->watchingGetName();

        // 这里抽成一个方法
        Helper::regExtCommandClass(static::class);
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
        
        if(!$this->isInHttpTest()) {
            return;
        }

        $this->options['name'] = $this->getTestName();       
        //// save list


        $before_run = Helper::SERVER('HTTP_X_MYCOVERAGE_BEFORERUN','');
        if($before_run){
            $this->callHandler($before_run);
        }
        
        $this->doBegin();
    }
    public function onAppPrepare()
    {
        $app = App::_();
        if(MyCoverageBridge::_()->isInHttpTest()){
            $app->options['ext_options_file'] = 'runtime/DuckPhpApps_test.config.php';
        } else if (MyCoverageBridge::_()->isInCliTest()){
            $app->options['ext_options_file'] = 'runtime/DuckPhpApps_test.config.php';
            $app->options['ext_options_file_enable'] = false;
        }
    }
    public function isInHttpTest()
    {
        $watching_name = $this->watchingGetName();
        $server_name = Helper::SERVER('HTTP_X_MYCOVERAGE_NAME',''); // do not use this;
        //$server_name = $_SERVER['HTTP_X_MYCOVERAGE_NAME']??'';
        if($watching_name === $server_name) {
            return true;
        }
        return false;
    }
    public function isInCliTest()
    {
        if (PHP_SAPI === 'cli' && App::Current()->options['cli_enable']) {
            $argv = Helper::SERVER('argv',[]);
            $cmd = $argv[1]??'NULL';
            if($cmd ==='testgroup'){
                return true;
            }
        }
        return false;
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
        
        $after_run = Helper::SERVER('HTTP_X_MYCOVERAGE_AFTERRUN','');
        if($after_run){
            $this->callHandler($after_run);

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
    protected function callHandler($handler,$ext_args=[])
    {
        if(!isset($handler)){
            return;
        }
        $handler = trim($handler);
        //$handler = "DuckAdmin\\Test\\Tester@_justTest?parameter=d";
        $flag = preg_match('/^(([a-zA-Z0-9_\x7f-\xff\\\\]+)(\:\:|\@|\->)([a-zA-Z0-9_\x7f-\xff]+)|([a-zA-Z0-9_\x7f-\xff]+))(\?(\S*))?$/' ,$handler,$m);
        if(!$flag){ 
            return false;
        }
        @list($_0,$_1,$class,$type,$method,$function,$_6,$parameters)=$m;
        return $this->callObject($class,$method,$type,$function,$parameters,$ext_args);
    }
    //////////////////
    protected function cleanClientStatus()
    {
        $this->is_save_session = true;
        $this->session_id = '';
    }
    protected function replay()
    {
        $this->cleanClientStatus();
        $this->doBegin();
        
        $this->options['name'] = 'replay';
        if($this->options['test_before_replay']){
            ($this->options['test_before_replay'])();
        }
        $callback = $this->options['test_list_callback'];
        $test_list = \call_user_func($callback);
        $test_list = \explode("\n",$test_list);
        
        foreach($test_list as $line){
            $this->readCommand($line);
        }
        $this->stopServer();
        
        if($this->options['test_after_replay']){
            ($this->options['test_after_replay'])();
        }
        $this->doEnd();
    }
    protected function readCommand($request)
    {
        $request = trim($request);
        if(!$request){
            return;
        }
        if(substr($request,0,2)==='##'){
            return;
        }
        if(substr($request,0,strlen('#CALL '))==='#CALL '){
            $this->explainCall($request);
        }
        if(substr($request,0,strlen('#WEB '))==='#WEB '){
            $this->explainWeb($request);
        }
        if(substr($request,0,strlen('#SETWEB '))==='#SETWEB '){
            $this->explainSetWeb($request);
        }
    }

    protected $pre_curl;
    protected $post_curl;
    protected $pre_webcall;
    protected $post_webcall;
    public function prepareCurl($ch)
    {
        $this->headers[] = 'X-MyCoverage-Name: '.$this->watchingGetName();
        if($this->pre_webcall){
            $this->headers[] = 'X-MyCoverage-BeforeRun: '.$this->pre_webcall;
            $this->pre_webcall=null;
        }
        if($this->post_webcall){
            $this->headers[] = 'X-MyCoverage-AfterRun: '.$this->post_webcall;
            $this->post_webcall=null;
        }
        $pre_curl =$this->pre_curl;
        $this->pre_curl = null;
        
        //////////////////////////
        if(!$pre_curl  || $pre_curl ==='_'){
            return $ch;
        }

        if($pre_curl ==='AJAX'){
            $this->headers[] = 'X-Requested-With: XMLHttpRequest';
            return $ch;
        }
        if($pre_curl ==='OPTIONS'){
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'OPTIONS');
            return $ch;
        }
        $this->callHandler($pre_curl, [$ch,'pre']);
        return $ch;
    }
    public function postpareCurl($ch)
    {
        $post_curl = $this->post_curl;
        $this->post_curl = null;
        
        $this->callHandler($post_curl, [$ch,'post']);
    }
    protected $headers =[];
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
        //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); 
        
        if($this->is_save_session){
            curl_setopt($ch, CURLOPT_HEADER, 1);
        }
        
        if(!empty($post)){
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
        }
        /////////
        if($this->session_id){
            curl_setopt($ch, CURLOPT_COOKIE, "PHPSESSID={$this->session_id}"); 
        }
        
        $this->prepareCurl($ch);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        $data = curl_exec($ch);
        $this->headers=[];
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
        $this->postpareCurl($ch);
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
        $data = $this->curl_file_get_contents([$url,'127.0.0.1'],$post,$is_ajax,$is_options,$method);
        if($this->options['test_echo_back']??false){
            echo substr($data,0,200);
        }
    }
    protected function explainCall($request)
    {
        @list($command,$func)=explode(' ',$request);
        if($command!=='#CALL'){return;}
        
        $this->options['name'] = $command;
        
        ////[[[[
        //// save list
        $path_dump = $this->getSubPath('path_dump');
        @mkdir($path_dump);
        file_put_contents($path_dump.$this->options['group'].'.list',$request ."\n",FILE_APPEND); 
        ////]]]]
        
        $this->callHandler($func);
        
    }
    protected function explainSetweb($request)
    {
        @list($command,$pre_curl,$pre_webcall,$post_webcall,$post_curl)=explode(' ',$request);
        if($command!=='#SETWEB'){return;}
        
        $this->pre_curl = ($pre_curl==='_')?null:$pre_curl;
        $this->pre_webcall = ($pre_webcall==='_')?null:$pre_webcall;
        $this->post_webcall = ($post_webcall==='_')?null:$post_webcall;
        $this->post_curl = ($post_curl==='_')?null:$post_curl;
        return;
    }
    ////////////////////////////////////////////////////////////////////////////
    public function callObject($class,$method,$type,$function,$poststr,$args = [])
    {
        $input = [];
        
        
        if($poststr){
            parse_str($poststr,$input);
        }
        if(!$function){
            $app = Helper::getAppClassByComponent($class);
            $last_phase = App::Phase($app::_()->getOverridingClass());
            if($type === '@'){
                $object = $class::_();
            }else if($type === '->'){
                $object = new $class;
            }else if($type === '::'){
                $object = $class;
            }
            $reflect = new \ReflectionMethod($object, $method);
        }else{
            $reflect = new \ReflectionFunction($function);
        }
        
        $params = $reflect->getParameters();
        foreach ($params as $i => $param) {
            $name = $param->getName();
            if (isset($input[$name])) {
                $args[$i] = $input[$name];
            } elseif ($param->isDefaultValueAvailable() && !isset($args[$i])) {
                $args[$i] = $param->getDefaultValue();
            } elseif (!isset($args[$i])) {
                throw new \ReflectionException("Command Need Parameter: {$name}\n", -2);
            }
        }
        $ret = $reflect->invokeArgs(is_object($object)? $object:null, $args);
        if(!$function){
            App::Phase($last_phase);
        }
        return $ret;
    }
    /**
     * tests group. use --help for more.
     */
    public function command_testgroup()
    {
        /*
        $handler = "DuckAdmin\\Test\\Tester@_justTest?parameter=d";
        $flag = preg_match('/^(([a-zA-Z0-9_\x7f-\xff\\\\]+)(\:\:|\@|\->)([a-zA-Z0-9_\x7f-\xff]+)|([a-zA-Z0-9_\x7f-\xff]+))(\?(\S*))?$/' ,$handler,$m);
        if(!$flag){ die("BAD");}
        list($_0,$_1,$class,$type,$method,$function,$_6,$parameters)=$m;

        $this->callObject($class,$method,$type,$function,$parameters);
        //$t= compact($_0,$_1,$class,$type,$method,$function,$_6,$paramter);
        //var_dump($t);
        exit;
        */
        $p = Console::_()->getCliParameters();
        if($p['help']??false){
            $str = <<<EOT
--watch {name}
--stop
--replay
--call SomeApp/Test/Tester@runX
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
            $this->options['group']=$p['watch'];
            echo "watching {$p['watch']}\n";
        }
        if($p['stop']??false){
            $this->watchingEnd();
        }
        if($p['replay']??false){
            $this->replay();
        }
        if($p['call']??false){
            if(is_string($p['call'])){
                $command = $p['call'];
                $this->options['name'] = 'call '.$command;
                $func=str_replace('/','\\',$command);
                $this->doBegin();
                try{
                    $this->callHandler($func);
                }catch(\Throwabl $ex){var_dump($ex);}
                $this->doEnd();
            }
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