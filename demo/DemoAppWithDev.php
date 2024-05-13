<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */
namespace Demo;

use DuckPhp\Core\Route;
use DuckPhp\DuckPhp;

use DuckPhp\Foundation\Helper;

class DemoAppWithDev extends DemoApp
{
    public function __construct()
    {
        parent::__construct();
        $path_src = realpath(__DIR__.'/../src/').'/';
        $this->options['ext'][MyCoverageBridge::class]=[
            'path_src'=> $path_src,
        ];
        $this->options['cli_command_classes'][]=MyCoverageBridge::class;
    }
    public function onInit()
    {
        parent::onInit();
    }
    public function action_index()
    {
        var_dump("DevMode!");
        Helper::Show([],'main');
    }
    public function command_foo($uri = '', $post = false)
    {
        $last_phase = static::Phase(\DuckAdmin\System\DuckAdminApp::class);
        $routes =$this->listAllRoutes();
        foreach($routes as $method =>$route){
            echo $route;
            echo "\n";
            //echo $route."\t\t\t\t".$method ."\n";
        }
        static::Phase($last_phase);
    }
    private function get_controller_path()
    {
        $namespace_prefix = Route::_()->getControllerNamespacePrefix();
        $welcome_class= $namespace_prefix .Route::_()->options['controller_welcome_class'] .Route::_()->options['controller_class_postfix']; 
        $ref = new \ReflectionClass($welcome_class);
        $path = dirname($ref->getFilename()).'/'; //DS;
        return $path;
    }
    private function get_all_controller_classes($path)
    {
        $controller_class_postfix = Route::_()->options['controller_class_postfix'];
        $namespace_prefix = Route::_()->getControllerNamespacePrefix();

        $directory = new \RecursiveDirectoryIterator($path, \FilesystemIterator::CURRENT_AS_PATHNAME | \FilesystemIterator::SKIP_DOTS);
        $iterator = new \RecursiveIteratorIterator($directory);
        $files = \iterator_to_array($iterator, false);
        
        $classes =[];
        foreach ($files as $file) {
            if(substr($file,-4)!=='.php'){continue;};
            $t = substr($file,strlen($path),-4);
            if ($controller_class_postfix && substr($t,0-strlen($controller_class_postfix))!==$controller_class_postfix) {
                continue;
            }
            $classes[]=$namespace_prefix.str_replace("/","\\",$t);
        }
        
        return $classes;
    }
    private function get_pathinfos($classes)
    {
        $namespace_prefix = Route::_()->getControllerNamespacePrefix();

        $ret =[];
        foreach($classes as $class){
            $full_class = $class;//$namespace_prefix.$class;
            
            $ref = new \ReflectionClass($full_class);
            $methods = $ref->getMethods(\ReflectionMethod::IS_PUBLIC);
            foreach($methods as $method){
                if($method->isStatic()){continue;}
                if($method->isConstructor()){continue;}
                $function = $method->getName();
                $path_info = $this->pathInfoFromClassAndMethod($full_class, $function);
                if(!isset($path_info)){continue;}
                $ret[$full_class.'->'.$function]=$path_info;
            }
        }
        return $ret;
    }
    protected function listAllRoutes()
    {
        $path = $this->get_controller_path();
        $classes = $this->get_all_controller_classes($path);
        $ret = $this->get_pathinfos($classes);
        return $ret;
    }
    protected function pathInfoFromClassAndMethod($class, $method, $adjuster = null)
    {
        $class_postfix = Route::_()->options['controller_class_postfix'];
        $method_prefix = Route::_()->options['controller_method_prefix'];
        
        $controller_welcome_class = Route::_()->options['controller_welcome_class'];
        $controller_welcome_method = Route::_()->options['controller_welcome_method'];
        $controller_path_ext = Route::_()->options['controller_path_ext'];
        $controller_url_prefix = Route::_()->options['controller_url_prefix'];
        
        $namespace_prefix = Route::_()->getControllerNamespacePrefix();
        
        if(substr($class,0,strlen($namespace_prefix))!== $namespace_prefix){
            return null;
        }
        if(substr($class,-strlen($class_postfix))!== $class_postfix){
            return null;
        }
        $first = substr($class,strlen($namespace_prefix),0-strlen($class_postfix));

        if($method_prefix && substr($method,0,strlen($method_prefix))!==$method_prefix){
            return null; // TODO do_
        }
        $last = substr($method,strlen($method_prefix));
        
        if($first === $controller_welcome_class && $last === $controller_welcome_method ){
            return $controller_url_prefix? $controller_url_prefix:'';
        }
        if($first===$controller_welcome_class){
            return $controller_url_prefix.$last.$controller_path_ext;
        }
        return $controller_url_prefix.$first. '/' .$last.$controller_path_ext;
        
    }
}
