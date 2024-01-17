<?php declare(strict_types=1);
use WorkermanHttpd\Request;
use WorkermanHttpd\Response;

// 目前版本的 WorkermanHttpd 和当前版本不匹配，我们修复他
class Httpd extends \WorkermanHttpd\WorkermanHttpd
{
    public static function _($object = null)
    {
        return static::G($object);
    }
    //@override to hot fix
    public function _OnMessage($connection, $request)
    {
        Request::G($request);
        Response::G(new Response());
        $this->doSuperGlobal($request);
        list($flag, $data) = $this->onRequest();
        Response::G()->withBody($data);
        
        ////
        $keep_alive = $request->header('connection');
        if (($keep_alive === null && $request->protocolVersion() === '1.1')
            || $keep_alive === 'keep-alive' || $keep_alive === 'Keep-Alive'
        ) {
            $connection->send(Response::G());
            $this->endSession();
            Response::G(new Response()); //free reference.
            Request::G(new \stdClass()); //free reference.
            return;
        }
        $connection->close(Response::G());  //  ---- THIS CODE IS OVERRIDE  TO FIX THIS
        $this->endSession();
        Response::G(new Response()); //free reference.
        Request::G(new \stdClass()); //free reference.
    }
    //@override to hot fix
    public function _header($output, bool $replace = true, int $http_response_code = 0)
    {
        $http_response_code = $http_response_code ? $http_response_code: 200;
        if ($http_response_code) {
            Response::G()->withStatus($http_response_code);
            // return; //  ---- THIS CODE IS OVERRIDE  TO FIX THIS
        }
        if(strpos($output, ':') !== false){
            @list($key, $value) = explode(':', $output);
            
            if(strtoupper($key) === 'CONTENT-TYPE'){
                $key = "Content-Type";
            }
            return Response::G()->header($key, $value)->withStatus($http_response_code);
        } else {
            return Response::G()->withStatus($http_response_code);
        }
        return Response::G()->header($key, $value)->withStatus($http_response_code);
    }
    //@override to hot fix
    public function _session_start(array $options = [])
    {
        //var_dump(\WorkermanHttpd\Request::G());
        $flag = \WorkermanHttpd\Request::G()->session();
        if(!$flag){return;}
        $_SESSION = \WorkermanHttpd\Request::G()->session()->all();
    }
    protected function runHttpAppClass()
    {
        $app = $this->options['http_app_class'];
        $flag = $app::_()->run();
        return true;
    }
}


class FixedWorkermanHttpd extends Httpd
{
    
    public function _exit($code = 0)
    {
        //这里的退出异常，应该使用 error, defind exit;
        throw new \DuckPhp\Core\ExitException('exit at'.DATE(DATE_ATOM),0);
        //throw new ExitException(''.$code, $code);
    }
    public function init(array $options, object $context = null)
    {
        //这里也是有要调整的地方，就这么调整了。
        $http_app_class = $options['http_app_class'];
        $http_app_class::_()->options['cli_enable']=false;
        
        $options['http_app_class']  = null; //这里出了状况。
        parent::init($options, $context);
        $this->options['http_app_class'] =$http_app_class ;
        
        
        
        //切换 SystemWrapper
        if(!defined('__SYSTEM_WRAPPER_REPLACER')){
            define('__SYSTEM_WRAPPER_REPLACER',static::class);
        }
        
        return $this;
    }    
}


































//*/
/*
\DuckPhp\Core\PhaseContainer::ReplaceSingletonImplement();
\DuckPhp\Core\PhaseContainer::GetContainerInstanceEx()->setDefaultContainer(\DuckPhp\DuckPhp::class);
\DuckPhp\Core\PhaseContainer::GetContainerInstanceEx()->setCurrentContainer(\DuckPhp\DuckPhp::class);
\DuckPhp\Core\PhaseContainer::GetContainerInstanceEx()->addPublicClasses([
    \WorkermanHttpd\WorkermanHttpd::class,
    \WorkermanHttpd\Request::class,
    \WorkermanHttpd\Response::class,
    \WorkermanHttpd\Worker::class,
]);
\WorkermanHttpd\Request::G(new FixedRequest());
//*/

/*
\WorkermanHttpd\WorkermanHttpd::G(FixedWorkermanHttpd::G());
\WorkermanHttpd\WorkermanHttpd::RunQuickly($options);

        $app = $this->options['http_app_class'];
        if($app){
            $app::G()->options['skip_404_handler'] = true;
            $app::assignExceptionHandler(ExitException::class, function () {
            });
            $app::system_wrapper_replace(static::system_wrapper_get_providers());
        }
//*/


/*
class FixedRequest extends \WorkermanHttpd\Request
{

    public function __construct($buffer='')
    {
        $this->_buffer = $buffer;
    }
}
        $options['http_app_class'] = $this->context_class;
        $options['path'] = $this->context()->options['path'];
        if (!empty($options['http_server'])) {
            $class = str_replace('/', '\\', $options['http_server']);
            HttpServer::_($class::_());
*/