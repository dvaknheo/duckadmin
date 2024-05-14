<?php declare(strict_types=1);
use WorkermanHttpd\WorkermanHttpd;

// 虽然升级到 1.0.5 但是还是不能完全隐藏，
class FixedWorkermanHttpd extends WorkermanHttpd
{
    
    public function _exit($code = 0)
    {
        //这里的退出异常，应该使用 error, defind exit;
        //DUCKPHP_EXIT_EXCEPTION
        \DuckPhp\Core\ExitException::Init();
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