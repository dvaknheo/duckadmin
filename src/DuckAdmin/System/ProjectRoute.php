<?php declare(strict_types=1);
/**
 * 系统的路由规则和这里的路由规则不一样，所以我们把他调整一下
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
                $this->route_error = "path_extention error";
                return [null];
            }
            $path_info = substr($path_info, 0, -$l);
        }
        
        $t = explode('/', $path_info);
        $method = array_pop($t);
        $path_class = implode('/', $t);
        
        
        $this->calling_path = $path_class?$path_info:$this->welcome_class.'/'.$method;

        
        if ($this->options['controller_hide_boot_class'] && $path_class === $this->welcome_class) {
            $this->route_error = "controller_hide_boot_class! {$this->welcome_class} ";
            return null;
        }
        $path_class = $path_class ?: $this->welcome_class;
        ////[[[[ 主要是这段改动
        $x= explode('/', $path_class);
        $baseclass = array_pop($x);
        $a=explode('-',$baseclass);
        $t=array_map(function($s){return ucfirst($s);},$a);
        $baseclass=implode('',$t);
        array_push($x,$baseclass);
        $path_class=implode('/',$x);
        ////]]]]
        $full_class = $this->namespace_prefix.str_replace('/', '\\', $path_class).$this->options['controller_class_postfix'];
        $full_class = ''.ltrim($full_class, '\\');
        return [$full_class,$method];
    }
}
