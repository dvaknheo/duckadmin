<?php declare(strict_types=1);
/**
 * 这里我们做一下
 */
namespace DuckAdmin\System;

use DuckPhp\Component\DbManager;
use DuckPhp\Component\RouteHookResource;
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
        //'controller_resource_prefix' => '/app/admin/',  // 资源文件前缀
        'ext_options_from_config' => true,
    ];
    public function __construct()
    {
        $this->options['path']=dirname(__DIR__).'/';
        parent::__construct();
    }
    public function onInit()
    {
        //为了满足 webman admin 的路由 替换掉默认的路由，这里牺牲了点效率
        ProjectRoute::G()->init(Route::G()->options,$this);
        Route::G(ProjectRoute::G());
        
        // 设置 Admin 为  admin 对象 ，让其他应用也能调 static::Root()::Admin()->isSuper
        $this->bumpAdmin(ActionApi::class));
        static::Admin(ActionApi::G());
        
        if (!$this->isInstalled()) {
            //这里临时客串一下虚拟资源
            Route::G()->options['controller_resource_prefix']='for_install/';
            RouteHookResource::G()->init([
                'path' => $this->options['path_override_from'],
                'path_resource' => 'res',
                'controller_url_prefix' => Route::G()->options['controller_url_prefix'],
                'controller_resource_prefix' => '/app/admin/for_install/',
            ], $this);
        }
        //如果根应用没设置数据，用自己的
        $this->switchDbManager();
    }
    public function install($options)
    {
        $this->installWithExtOptions($options);
        //TODO 接着复制 res 文件
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
    // 这是给 外面提供的
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

    // 这段是复制文件用的。
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
