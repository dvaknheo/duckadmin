<?php
namespace DuckAdmin\Business;

use DuckPhp\Component\DbManager;

use DuckAdmin\Business\Base;
use DuckAdmin\Business\Base as Helper;

use DuckAdmin\Model\RuleModel;
use DuckAdmin\Model\AdminModel;
use DuckAdmin\Model\AdminRoleModel;
use DuckAdmin\System\DuckAdminApp;


/**
 * 个人资料业务
 */
class InstallBusiness extends Base
{
    public function isInstalled()
    {
        //TODO 放到 Helper 里
        return DuckAdminApp::_()->isInstalled();
    }
    protected function getConfigFile($file)
    {
        //TODO 放到 Helper 里
        return DuckAdminApp::_()->getFileFromSubComponent(DuckAdminApp::_()->options, 'config', $file);
    }
    protected function checkDatabase()
    {
        $options = DbManager::_()->options;
        if (!empty($options['database']) || !empty($options['database_list'])){
            return true;
        }
        return false;
    }
    protected function checkInstallLogFile()
    {
        // no need
        return false;
    }
    protected function writeDbConfigFile($post)
    {
        $options = [];
        $options['database'] = [
            'dsn'=>"mysql:host={$post['host']};port={$post['port']};dbname={$post['database']};charset=utf8mb4;",
            'username' => $post['user'],	
            'password' => $post['password'],
        ];
        DuckAdminApp::_()->install($options);
    }
    ///////////////////////////
    protected function initSql()
    {
        $sql_file = $this->getConfigFile('install.sql');
        Helper::ThrowOn( !isset($sql_file),  '数据库SQL文件不存在',1);
        $sql_query = file_get_contents($sql_file);
        
        $sql_query = $this->removeComments($sql_query);
        $sql_query = $this->splitSqlFile($sql_query, ';');
        
        foreach ($sql_query as $sql) {
            DbManager::Db()->execute($sql);
        }
        
    }
    protected function checkTableOverwrite($tables, $overwrite)
    {
        $tables_to_install = [
            'wa_admins',
            'wa_admin_roles',
            'wa_roles',
            'wa_rules',
            'wa_options',
            'wa_users',
            'wa_uploads',
        ];

        $tables_exist = [];
        foreach ($tables as $table) {
            $tables_exist[] = current($table);
        }
        $tables_conflict = array_intersect($tables_to_install, $tables_exist);
        if (!$overwrite) {
            Helper::ThrowOn( $tables_conflict, '以下表' . implode(',', $tables_conflict) . '已经存在，如需覆盖请选择强制覆盖',1);
        } else {
            foreach ($tables_conflict as $table) {
                DbManager::Db()->execute("DROP TABLE `$table`");
            }
        }
    }
    protected  function createDatabase($post)
    {
        // 首先我们要把 DbManager 搞出来，重新初始化。
        // 如果 DbManager 的数据已设置，我们就用里面的数据，不然我们用自己额外的数据
        $old = DbManager::G();
        $options = $old->options;

        $options['database']=[
            'dsn'=>"mysql:host={$post['host']};port={$post['port']};dbname={$post['database']};charset=utf8;",
            'username'=>$post['user'],	
            'password'=>$post['password'],
        
        ];
        DbManager::G(new DbManager())->init($options);
        $database=$post['database'];
        $data = DbManager::Db()->fetchAll("show databases like '$database'");
        if (!$data){
            DbManager::Db()->execute("create database $database");
        }
        DbManager::Db()->execute("use $database");
        $tables = DbManager::Db()->fetchAll("show tables");
        return $tables;
    }
    public function step1($post)
    {
        $user = $post['user'];
        $password = $post['password'];
        $database = $post['database'];
        $host =  $post['host'];
        $port = $post['port'];
        $overwrite = $post['overwrite'];
        
        $flag = $this->isInstalled();
        Helper::ThrowOn(!$overwrite && $flag ,'管理后台已经安装！如需重新安装，请删除该插件数据库配置文件并重启',1);

        try {
            $tables = $this->createDatabase($post); // 这段是系统程序员的活了
        } catch (\Throwable $e) {
            Helper::ThrowOn(stripos($e, 'Access denied for user'), '数据库用户名或密码错误',2);
            Helper::ThrowOn(stripos($e, 'Connection refused'), 'Connection refused. 请确认数据库IP端口是否正确，数据库已经启动',1);
            Helper::ThrowOn(stripos($e, 'timed out'), '数据库连接超时，请确认数据库IP端口是否正确，安全组及防火墙已经放行端口',1);
            throw $e;
        }
        $this->checkTableOverwrite($tables,$overwrite);
        $this->initSql();
        
        // FirstRole，第一个角色我们要从 initSQL 中抽取出来。
        
        // 导入菜单
        $menus =  static::Config('menu',null,[]);
        RuleModel::_()->importMenu($menus);
        
        $this->writeDbConfigFile($post);
    }


    /**
     * 去除sql文件中的注释
     * @param $sql
     * @return string
     */
    protected function removeComments($sql): string
    {
        return preg_replace("/(\n--[^\n]*)/","", $sql);
    }

    /**
     * 分割sql文件
     * @param $sql
     * @param $delimiter
     * @return array
     */
    protected function splitSqlFile($sql, $delimiter): array
    {
        $tokens = explode($delimiter, $sql);
        $output = array();
        $matches = array();
        $token_count = count($tokens);
        for ($i = 0; $i < $token_count; $i++) {
            if (($i != ($token_count - 1)) || (strlen($tokens[$i] > 0))) {
                $total_quotes = preg_match_all("/'/", $tokens[$i], $matches);
                $escaped_quotes = preg_match_all("/(?<!\\\\)(\\\\\\\\)*\\\\'/", $tokens[$i], $matches);
                $unescaped_quotes = $total_quotes - $escaped_quotes;

                if (($unescaped_quotes % 2) == 0) {
                    $output[] = $tokens[$i];
                    $tokens[$i] = "";
                } else {
                    $temp = $tokens[$i] . $delimiter;
                    $tokens[$i] = "";

                    $complete_stmt = false;
                    for ($j = $i + 1; (!$complete_stmt && ($j < $token_count)); $j++) {
                        $total_quotes = preg_match_all("/'/", $tokens[$j], $matches);
                        $escaped_quotes = preg_match_all("/(?<!\\\\)(\\\\\\\\)*\\\\'/", $tokens[$j], $matches);
                        $unescaped_quotes = $total_quotes - $escaped_quotes;
                        if (($unescaped_quotes % 2) == 1) {
                            $output[] = $temp . $tokens[$j];
                            $tokens[$j] = "";
                            $temp = "";
                            $complete_stmt = true;
                            $i = $j;
                        } else {
                            $temp .= $tokens[$j] . $delimiter;
                            $tokens[$j] = "";
                        }

                    }
                }
            }
        }

        return $output;
    }

    ////////////
    /**
     * 设置管理员
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function step2($username,$password,$password_confirm)
    {
        Helper::ThrowOn($password !== $password_confirm, '两次密码不一致',1);
        $flag = $this->checkDataBase();
        Helper::ThrowOn(!$flag, '请先完成第一步数据库配置', 1);
        $flag = AdminModel::_()->hasAdmins();
        Helper::ThrowOn($flag, '后台已经安装完毕，无法通过此页面创建管理员',1);
        
        try{
        $admin_id = AdminModel::_()->addFirstAdmin($username, $password);
        AdminRoleModel::_()->addFirstRole($admin_id);
        }catch(\Throwable $ex){
            var_dump($ex);
        }
    }


}