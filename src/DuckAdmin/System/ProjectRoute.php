<?php declare(strict_types=1);
/**
 * 系统的路由规则和这里的路由规则不一样，所以我们把他调整一下
 * From this time, you never be alone~
 */
namespace DuckAdmin\System;

use DuckPhp\Core\Route;
use DuckPhp\Core\App;

class ProjectRoute extends Route
{
    protected function pathToClassAndMethod($path_info)
    {
        list($full_class, $method) = parent::pathToClassAndMethod($path_info);
        if($full_class===null){ return [$full_class, $method];}
        
        // 因为 webman-admin 的控制器命名方式和我们的有所不同 :(
        //DuckAdmin\Controller\aa-bbController => DuckAdmin\Controller\AaBbController
        $prefix = "DuckAdmin\\Controller\\";
        if(substr($full_class,0,strlen($prefix))===$prefix){
            $class = substr($full_class,strlen($prefix));
            $class = ucfirst(preg_replace_callback('/-([a-z])/',function($m){return ucfirst($m[1]);},$class));
            
            $full_class = $prefix.$class;
        }
        return [$full_class,$method];
    }
    public function pathFromClassAndMethod($full_class, $method,...$args)
    {
        //我们要完成这个逆函数,并放到 Core/Route 里
        $prefix = $this->getControllerNamespacePrefix();
        $class = substr($full_class,$prefix);
        $path = $class .'/'. $method;
        return $path;
    }
}
