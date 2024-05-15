<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */
namespace Demo;

use DuckPhp\Core\App;
use DuckPhp\Core\ComponentBase;
use DuckPhp\Core\Console;
use DuckPhp\Core\EventManager;
use DuckPhp\Foundation\Helper;

class TestFileCreator extends ComponentBase
{
    //////////////////////////
    protected function get_component_path($component)
    {
        $base_class = App::Current()->options['namespace']."\\{$component}\\Base";
        $ref = new \ReflectionClass($base_class);
        $path = dirname($ref->getFilename()).'/';
        return $path;
    }
    protected function get_all_component_classes($path, $component)
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
    public function getBusinessTestString($path, $file, $namespace)
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
        $files = $this->get_all_component_classes($path,$component);
        $namespace = App::Current()->options['namespace']."\\{$component}\\";
        $data =[];
        foreach($files as $file){
            $data[]= $this->getBusinessTestString($path,$file,$namespace);
        }
        return implode("\n",$data)."\n";
    }
    public function command_foo2()
    {
        $last_phase = App::Phase(\DuckAdmin\System\DuckAdminApp::class);
        $str = $this->getAllComponentTestTemplate('Controller');
        echo $str;
        App::Phase($last_phase);
    }
}