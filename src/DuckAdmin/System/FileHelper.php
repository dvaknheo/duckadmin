<?php declare(strict_types=1);
/**
 * 这里我们做一下
 */
namespace DuckAdmin\System;
use DuckPhp\SingletonEx\SingletonExTrait;


/**
 * 这是复制文件用的。
 */
class FileHelper
{
    use SingletonExTrait;
    // 
    
    protected function getDestDir($path_parent,$path )
    {
        $new_dir = $path_parent;
        $b = explode('/',$path);
        
        foreach($b as $v){
            $new_dir .= '/'.$v;
            if(is_dir($new_dir)){ continue;}
            mkdir($new_dir);
        }
        return $new_dir;
    }
    public function copyDir($source, $path_parent,$path, $force = false, &$info ='')
    {
        $dest = $this->getDestDir($path_parent,$path);
        
        $source = rtrim(''.realpath($source), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
        $dest = rtrim(''.realpath($dest), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
        $directory = new \RecursiveDirectoryIterator($source, \FilesystemIterator::CURRENT_AS_PATHNAME | \FilesystemIterator::SKIP_DOTS);
        $iterator = new \RecursiveIteratorIterator($directory);
        $t_files = \iterator_to_array($iterator, false);
        $files = [];
        
        foreach ($t_files as $file) {
            $short_file_name = substr($file, strlen($source));
            $files[$file] = $short_file_name;
        }
        
        if (!$force) {
            $flag = $this->check_files_exist($source, $dest, $files, $info);
            if (!$flag) {
                return; // @codeCoverageIgnore
            }
        }
        $info.= "Copying file...\n";
        
        $flag = $this->create_directories($dest, $files, $info);
        if (!$flag) {
            return; // @codeCoverageIgnore
        }
        $is_in_full = false;
        
        foreach ($files as $file => $short_file_name) {
            $dest_file = $dest.$short_file_name;
            $data = file_get_contents(''.$file);
            $flag = file_put_contents($dest_file, $data);
            
            $info.= $dest_file."\n";
            //decoct(fileperms($file) & 0777);
        }
        //echo  "\nDone.\n";
    }
    protected function check_files_exist($source, $dest, $files, &$info)
    {
        foreach ($files as $file => $short_file_name) {
            $dest_file = $dest.$short_file_name;
            if (is_file($dest_file)) {
                $info.= "file exists: $dest_file \n";
                return false;
            }
        }
        return true;
    }
    protected function create_directories($dest, $files, &$info)
    {
        foreach ($files as $file => $short_file_name) {
            // mkdir.
            $blocks = explode(DIRECTORY_SEPARATOR, $short_file_name);
            array_pop($blocks);
            $full_dir = $dest;
            foreach ($blocks as $t) {
                $full_dir .= DIRECTORY_SEPARATOR.$t;
                if (!is_dir($full_dir)) {
                    try{
                        $flag = mkdir($full_dir);
                    }catch(\Throwable $ex) {                               // @codeCoverageIgnore
                        $info .= "create file failed: $full_dir \n";// @codeCoverageIgnore
                        return false;   // @codeCoverageIgnore
                    }
                }
            }
        }
        return true;
    }
}