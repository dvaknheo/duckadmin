<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */
use WorkermanHttpd\Request;
use WorkermanHttpd\Response;
require_once(__DIR__.'/../vendor/autoload.php');

////[[[[
// 这里用本地的最新版本 DuckPhp 方便测试可调。
if(is_file(__DIR__.'/../../DNMVCS/autoload.php')){
    $funcs = spl_autoload_functions();
    $t =$funcs[0];
    spl_autoload_unregister($t);
    @include_once(__DIR__.'/../../DNMVCS/src/Core/AutoLoader.php');    // 这里用本地的最新版本 DuckPhp 方便测试可调。
    spl_autoload_register([DuckPhp\Core\AutoLoader::class ,'DuckPhpSystemAutoLoader']);
    spl_autoload_register($t);
}
////]]]]

//*
class FixedRequest extends \WorkermanHttpd\Request
{

    public function __construct($buffer='')
    {
        $this->_buffer = $buffer;
    }
}
class FixedWorkermanHttpd extends \WorkermanHttpd\WorkermanHttpd
{
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
            Response::G(new \stdClass()); //free reference.
            Request::G(new \stdClass()); //free reference.
            return;
        }
        $connection->close(Response::G());  //  ---- THIS CODE IS OVERRIDE  TO FIX THIS
        $this->endSession();
        Response::G(new \stdClass()); //free reference.
        Request::G(new \stdClass()); //free reference.
    }
    public function _header($output, bool $replace = true, int $http_response_code = 0)
    {
        if ($http_response_code) {
            Response::G()->withStatus($http_response_code);
            // return; //  ---- THIS CODE IS OVERRIDE  TO FIX THIS
        }
        @list($key, $value) = explode(':', $output);
        return Response::G()->header($key, $value)->withStatus($http_response_code);
    }
    
    public function _session_start(array $options = [])
    {
        //var_dump(\WorkermanHttpd\Request::G());
        $flag = \WorkermanHttpd\Request::G()->session();
        if(!$flag){return;}
        $_SESSION = \WorkermanHttpd\Request::G()->session()->all();
    }
}
//*/
function onInit()
{
    // 我们需要热修一下，以及除了 宏改变导致的问题

    \DuckPhp\Core\SystemWrapper::system_wrapper_replace(\WorkermanHttpd\WorkermanHttpd::system_wrapper_get_providers());
    \DuckPhp\Core\SystemWrapper::system_wrapper_replace(['exit'=>function(){
        throw new \DuckPhp\Core\ExitException('exit at'.DATE(DATE_ATOM),0);
        return;
    }]);
}
function myworker()
{

$options = [
    'cli_enable'=>false,
    'is_debug'=>true,
    'ext' => [
        // 后台管理系统
//*/
        \DuckAdmin\System\DuckAdminApp::class => [
            'controller_url_prefix' => 'app/admin/', // 访问路径
            'controller_resource_prefix' => 'res/',  // 资源文件前缀
        ],
        \DuckUser\System\DuckUserApp::class => [
            'controller_url_prefix' => 'user/', // 访问路径
            'controller_resource_prefix' => 'res/',  // 资源文件前缀
        ],
//*/
        \DuckUserManager\System\DuckUserManagerApp::class => [
            'controller_url_prefix' => 'app/admin/', // 访问路径
            'controller_resource_prefix' => 'res/',  // 资源文件前缀
        ],
//*/
        \SimpleBlog\System\SimpleBlogApp::class => [
            'controller_url_prefix' => 'blog/', // 访问路径
            'controller_resource_prefix' => 'res/',  // 资源文件前缀
        ],
//*/
    ],
    'on_init'=> 'onInit',
];
$options['path'] = __DIR__.'/';
$options['welcome_view'] = 'main';
\DuckPhp\DuckPhp::InitAsContainer($options)->run();
//SystemWrapper::_()->_system_wrapper_get_providers();


        return true;
}
$options =[
    'http_handler' => 'myworker',
    'http_handler_root' => '',
    'with_http_handler_file' => true,
    'http_app_class'=>null,
    //'request_class'=>FixedRequest::class,
];
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
\WorkermanHttpd\WorkermanHttpd::G(FixedWorkermanHttpd::G());

\WorkermanHttpd\WorkermanHttpd::RunQuickly($options);