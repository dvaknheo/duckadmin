<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckAdmin\System;

use DuckPhp\Core\Route;

class ProjectRoute extends Route
{
	protected function pathToClassAndMethod($path_info)
	{
        $path_info = ltrim((string)$path_info, '/');
        if (!empty($this->options['controller_path_ext']) && !empty($path_info)) {
            $l = strlen($this->options['controller_path_ext']);
            if (substr($path_info, -$l) !== $this->options['controller_path_ext']) {
                $this->runtime()->route_error = "path_extention error";
                return [null];
            }
            $path_info = substr($path_info, 0, -$l);
        }
        
        $t = explode('/', $path_info);
        $method = array_pop($t);
        $path_class = implode('/', $t);
		
        
        $this->runtime()->calling_path = $path_class?$path_info:$this->welcome_class.'/'.$method;

        
        if ($this->options['controller_hide_boot_class'] && $path_class === $this->welcome_class) {
            $this->runtime()->route_error = "controller_hide_boot_class! {$this->welcome_class} ";
            return null;
        }
        $path_class = $path_class ?: $this->welcome_class;
		////[[[[
		$x= explode('/', $path_class);
		$baseclass = array_pop($x);
		$a=explode('-',$baseclass);
		$t=array_map(function($s){return ucfirst($s);},$a);
		$baseclass=implode('',$t);
		array_push($x,$baseclass);
		$path_class=implode('/',$x);
		$this->options['controller_class_postfix']='Controller';
		////]]]]
        $full_class = $this->namespace_prefix.str_replace('/', '\\', $path_class).$this->options['controller_class_postfix'];
        $full_class = ''.ltrim($full_class, '\\');
		var_dump($full_class);
		return [$full_class,$method];
	}
    public function defaultGetRouteCallback($path_info)
    {
        $this->runtime()->route_error = '';
		list($full_class,$method)=$this->pathToClassAndMethod($path_info);
		if($full_class===null){
			return null;
		}
        $this->runtime()->calling_class = $full_class;
        $this->runtime()->calling_method = !empty($method)?$method:$this->index_method;
        
        ////////////////////////
        try {
            /** @var class-string */ $class = $full_class;
            if ($full_class !== (new \ReflectionClass($class))->getName()) {
                $this->runtime()->route_error = "can't find class($full_class) by $path_info .";
                return null;
            }
        } catch (\ReflectionException $ex) {
            $this->runtime()->route_error = "can't Reflection class($full_class) by $path_info .";
            return null;
        }
        /** @var string */ $base_class = str_replace('~', $this->namespace_prefix, $this->options['controller_base_class']);
        if (!empty($base_class) && !is_subclass_of($full_class, $base_class)) {
			$this->runtime()->route_error = "no the controller_base_class! {$base_class} ";
			return null;
		
        }
        
        $object = $this->createControllerObject($full_class);
        $route_error = $this->runtime()->route_error;
        // @phpstan-ignore-next-line
        if ($route_error) {
            return null;
        }
        return $this->getMethodToCall($object, $method);
    }

    
}
