<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckCommon;

// 这个类会通用化

use DuckPhp\Core\App;
use DuckPhp\Core\Configer;
use DuckPhp\Core\ComponentBase;
use DuckPhp\Foundation\SqlDumper;
use DuckPhp\Foundation\ThrowOnableTrait;

class Installer extends ComponentBase
{
    const NEED_DATABASE = 1;
    const NEED_INSTALL = 2;
    const NEED_OTHER = 3;
    
    use ThrowOnableTrait;
    
    public $options = [
        'install_lock_file' => 'installer.lock',
        'force' => false,
        
            'sql_dump_prefix' => '',
            'sql_dump_install_replace_prefix' => true,
            'sql_dump_install_drop_old_table' => false,
    ];
    protected $path_lock;
    public function __construct()
    {
        parent::__construct();
        $this->exception_class = NeedInstallException::class;
    }
    //@override
    public function init(array $options, ?object $context = NULL)
    {
        parent::init($options, $context);
        
        $this->path_lock = $this->getComponenetPath(Configer::G()->options['path_config'],Configer::G()->options['path']);
        $path = $context->plugin_options['plugin_path'] ?: $context->options['path'];
        $path_config = $path.'config/';
        $this->options['path_sql_dump'] = $path_config;
        
        SqlDumper::G()->init($options, ($this->context_class)::G());
        
        return $this;
    }
    public function isInstalled()
    {
        $file = $this->path_lock.$this->options['install_lock_file'];
        return is_file($file);
    }
    ////////////////

    public static function CheckInstall($options,$context, $has_database)
    {
        return static::G()->_CheckInstall($options,$context ,$has_database)
    }
    public  function _CheckInstall($options,$context,$has_database)
    {
         static::ThrowOn(!$has_database, '你需要外部配置，如数据库等',static::NEED_DATABASE);
    
        $flag = static::G()->init([],$this)->isInstalled()){
        static::ThrowOn(!$flag,"你需要安装",static::NEED_INSTALL);
    }
    //////////////////
    protected function writeLock()
    {
        $file = $this->path_lock.$this->options['install_lock_file'];
        return @file_put_contents($file,DATE(DATE_ATOM));
    }
    public function run()
    {
        $ret = false;
        if(!$this->options['force'] && $this->isInstalled()){
           static::ThrowOn(true,'你已经安装 !',-1);     
        }
        try{
            $ret = SqlDumper::G()->install($this->options['force']??false);
        }catch(\Exception $ex){
            static::ThrowOn(true, "写入数据库失败:" . $ex->getMessage(),-2);
        }
        if($ret){
            return $ret;
        }
        $flag = $this->writeLock();
        static::ThrowOn(!$flag,'写入锁文件失败',-3);
            
        return $ret;
    }
    public function dumpSql()
    {
        return SqlDumper::G()->run();
    }
    /////////////////////
    protected function getComponenetPath($path, $basepath = ''): string
    {
        // 考虑放到系统里
        if (DIRECTORY_SEPARATOR === '/') {
            if (substr($path, 0, 1) === '/') {
                return rtrim($path, '/').'/';
            } else {
                return $basepath.rtrim($path, '/').'/';
            }
        } else { // @codeCoverageIgnoreStart
            if (substr($path, 1, 1) === ':') {
                return rtrim($path, '\\').'\\';
            } else {
                return $basepath.rtrim($path, '\\').'\\';
            } // @codeCoverageIgnoreEnd
        }
    }
}