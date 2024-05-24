<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */
namespace Demo\Test;

use DuckPhp\Core\App;
use DuckPhp\Core\ComponentBase;
use DuckPhp\Core\Console;
use DuckPhp\Core\EventManager;
use DuckPhp\Core\Route;
use DuckPhp\Foundation\Helper;

class TestFileCreator extends ComponentBase
{
    //////////////////////////
    protected function get_component_path($component,$base_file = 'Base')
    {

        $base_class = App::Current()->options['namespace']."\\{$component}\\{$base_file}";
        $ref = new \ReflectionClass($base_class);
        $path = dirname($ref->getFilename()).'/';
        return $path;
    }

    protected function get_all_component_classes_files($path, $component)
    {
        $directory = new \RecursiveDirectoryIterator($path, \FilesystemIterator::CURRENT_AS_PATHNAME | \FilesystemIterator::SKIP_DOTS);
        $iterator = new \RecursiveIteratorIterator($directory);
        $files = \iterator_to_array($iterator, false);
        
        $ret = [];
        foreach ($files as $file) {
            if(substr($file,-strlen($component.'.php'))!==$component.'.php'){continue;};
            $ret[] = $file;
        }
        return $ret;
    }
    public function getComponentTestString($path, $file, $namespace)
    {
        $data = file_get_contents($file);
        preg_match_all('/public\s+function (([^\(]+)\([^\)]*\))/', (string)$data, $m);
        $funcs = $m[1];
        
        $ret = '';
        $class = substr($file,strlen($path),-strlen('.php'));
        $class = $namespace .str_replace('/','\\',$class);
        foreach ($funcs as $v) {
            $v = str_replace(['&','callable '], ['',''], $v);
            $ret .= "        \\{$class}::_()->$v;\n";
        }
        return $ret;
    }
    public function getAllComponentTestTemplate($component)
    {
        $path = $this->get_component_path($component);
        $files = $this->get_all_component_classes_files($path,$component);
        $namespace = App::Current()->options['namespace']."\\{$component}\\";
        $data =[];
        foreach($files as $file){
            $data[]= $this->getComponentTestString($path,$file,$namespace);
        }
        return implode("\n",$data)."\n";
    }
    ////////////////[[[[[[[[[[[
    public function dump()
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

    private function get_all_controller_classes($path)
    {
        $controller_class_postfix = Route::_()->options['controller_class_postfix'];
        $namespace_prefix = Route::_()->getControllerNamespacePrefix();

        $files = $this->get_all_component_classes_files($path,$controller_class_postfix);
        
        $classes =[];
        foreach ($files as $file) {
            $t = substr($file,strlen($path),-4);
            if ($controller_class_postfix && substr($t,0-strlen($controller_class_postfix))!==$controller_class_postfix) {
                continue;
            }
            $classes[] = $namespace_prefix.str_replace("/","\\",$t);
        }
        
        return $classes;
    }
    private function get_pathinfos($classes, $adjuster = null)
    {
        $namespace_prefix = Route::_()->getControllerNamespacePrefix();

        $ret =[];
        foreach($classes as $class){
            $full_class = $class;
            $ref = new \ReflectionClass($full_class);
            $methods = $ref->getMethods(\ReflectionMethod::IS_PUBLIC);
            foreach($methods as $method){
                if($method->isStatic()){continue;}
                if($method->isConstructor()){continue;}
                $function = $method->getName();
                $path_info = $this->pathInfoFromClassAndMethod($full_class, $function, $adjuster);
                if(!isset($path_info)){continue;}
                $ret[$full_class.'->'.$function]=$path_info;
            }
        }
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
        

        if($adjuster){
            $first = call_user_func($adjuster, $first);
        }
        
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
    /////////////////]]]]]]]]]]
    public function command_foo2()
    {
        $last_phase = App::Phase(\DuckAdmin\System\DuckAdminApp::class);
        $str = $this->getAllComponentTestTemplate('Controller');
        echo $str;
        App::Phase($last_phase);
    }
    public function listAllRoutes($adjuster=null)
    {
        /*
        $namespace_prefix = Route::_()->getControllerNamespacePrefix();
        $welcome_class= $namespace_prefix .Route::_()->options['controller_welcome_class'] .Route::_()->options['controller_class_postfix']; 
        */
        
        $path = $this->get_component_path('Controller','Base');
        $classes = $this->get_all_controller_classes($path);
        
        $ret = $this->get_pathinfos($classes, $adjuster);
        return $ret;
    }
    public function genRunFile()
    {
        $routes = $this->listAllRoutes();
        $routes = array_values($routes);
        $routes = implode("\n",$routes);
        
        $controllers = $this->getAllComponentTestTemplate('Controller');
        $business = $this->getAllComponentTestTemplate('Business');
        $models = $this->getAllComponentTestTemplate('Model');
        $replaces =[
            '@routes' => $routes,
            '@controllers' => $controllers,
            '@business' => $business,
            '@models' => $models,
        ];
        $template = file_get_contents(__DIR__.'/TestFileCreator.php.template');
        foreach($replaces  as $k => $v) {
            $template = str_replace($k,$v,$template);
        }
        //$template = str_replace(array_keys($replaces),array_values($replaces),$template);
        file_put_contents(__DIR__.'/output.php',$template);
        
    }
    
    private $phase_map =[];
    private function get_all_namepace_phase_map()
    {
        $classes = PhaseContainer::GetContainerInstanceEx()->publics;
        $phase_map =[];
        foreach($classes as $class =>$_){
            if(!isset($class::_()->options['namespace'])){continue;}
            $phase_map[$class::_()->options['namespace'].'\\' ]= $class;
        }
        return $phase_map;
    }
    public function phaseFromClass($class)
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
}