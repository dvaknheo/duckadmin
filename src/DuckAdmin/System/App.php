<?php declare(strict_types=1);
/**
 * 这里我们做一下
 */
namespace DuckAdmin\System;

use DuckPhp\Component\DbManager;
use DuckPhp\Core\Route;
use DuckPhp\DuckPhp;

/**
 * 入口类
 */
class App extends DuckPhp
{
    //@override
    public $options = [
        'is_debug' => true,
        'error_404' => '_sys/error_404',
        'error_500' => '_sys/error_500',
        'controller_class_postfix' => 'Controller', // 控制器后缀
        'controller_resource_prefix' => '/res/',  // 资源文件前缀
        'ext_options_from_config' => true,
    ];
    public function __construct()
    {
        $this->options['path']=dirname(__DIR__).'/';
        parent::__construct();
    }
    public function onInit()
    {
        //替换掉默认的路由，这里牺牲了点效率
        ProjectRoute::G()->init(Route::G()->options,$this);
        Route::G(ProjectRoute::G());

        // 设置 admin 为  admin 对象
        static::Root()::Admin($this->createPhaseProxy(ActionApi::class));
        static::Admin(ActionApi::G());
        
        if (!$this->isInstalled()) {
            //TODO 如果没安装，暂时使用虚拟的 res
        }
        
        //如果主控程序没设置数据，用自己的
        $this->switchDbManager();
    }
    public function install($options)
    {
        $this->installWithExtOptions($options);
        //接着复制 res 文件
    }
    ////////////// 命令行
    public function command_install()
    {
        // 安装命令。
        echo "welcome to Use DuckAdmin installer  --force  to force install\n";
        $parameters =  static::Parameter();
        if(count($parameters)==1 || ($parameters['help'] ?? null)){
            // echo "--force  to force install ;";
            //return;
        }
        //echo $this->install($parameters); // 一些安装动作，这里还没想好
        echo "Done \n";
    }
    protected function switchDbManager()
    {
        $options = DbManager::G()->options;
        if (!empty($options['database']) && !empty($options['database_list'])){
            return;
        }
        $post = $this->options['database'] ?? null;
        if (empty($post)) {
            return;
        }
        $options['database']=$post;
        $options['force']=true;
        
        DbManager::G( )->init($options, static::Root());
    
    }
    public function checkDatabase()
    {
        $options = DbManager::G()->options;
        if(empty($options['database']) || empty($options['database_list'])){
            $post = $this->options['database']?? false;
            if (!$post) {
                return false;
            }
        }
        return true;
    }

    //////////////////
    public function getFileFromSubComponent($subkey,$file)
    {
        clearstatcache();
        //$this->options['path'];
        $path = $this->options['path_'.$subkey];
        $full_file = $path.$file;
        
        if(is_file($full_file)){
            return $full_file;
        }
        $path = $this->options['path_'.$subkey.'_override_from'] ?? null;
        if (!isset($path)) {
            return null;
        }
        $full_file = $path.$file;
        
        if(is_file($full_file)){
            return $full_file;
        }
        return null;
    }
    
    public function dumpDir($source, $dest, $force = false, &$info ='')
    {
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
            $flag = $this->check_files_exist($source, $dest, $files);
            if (!$flag) {
                return; // @codeCoverageIgnore
            }
        }
        $info.= "Copying file...\n";
        
        $flag = $this->create_directories($dest, $files);
        if (!$flag) {
            return; // @codeCoverageIgnore
        }
        $is_in_full = false;
        
        foreach ($files as $file => $short_file_name) {
            $dest_file = $dest.$short_file_name;
            $data = file_get_contents(''.$file);
            $data = $this->filteText($data, $is_in_full, $short_file_name);
            $flag = file_put_contents($dest_file, $data);
            
            $info.= $dest_file."\n";
            //decoct(fileperms($file) & 0777);
        }
        //echo  "\nDone.\n";
    }
    protected function check_files_exist($source, $dest, $files, $info)
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
    protected function create_directories($dest, $files)
    {
        foreach ($files as $file => $short_file_name) {
            // mkdir.
            $blocks = explode(DIRECTORY_SEPARATOR, $short_file_name);
            array_pop($blocks);
            $full_dir = $dest;
            foreach ($blocks as $t) {
                $full_dir .= DIRECTORY_SEPARATOR.$t;
                if (!is_dir($full_dir)) {
                    $flag = mkdir($full_dir);
                    if (!$flag) {                               // @codeCoverageIgnore
                        //echo "create file failed: $full_dir \n";// @codeCoverageIgnore
                        return false;   // @codeCoverageIgnore
                    }
                }
            }
        }
        return true;
    }
}
