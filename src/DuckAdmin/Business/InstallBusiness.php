<?php
namespace DuckAdmin\Business;

/**
 * 个人资料业务
 */
class InstallBusiness extends BaseBusiness 
{
	public function isInstalled()
	{
		// 这里应该拆分成两个 本类用的和上一个类用的
		$database_config_file = base_path() . '/plugin/admin/config/database.php';
        $flag = is_file($database_config_file);
		
		return true;
	}
	protected function checkTableOverwrite($overwrite)
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
			static::ThrowOn( $tables_conflict, '以下表' . implode(',', $tables_conflict) . '已经存在，如需覆盖请选择强制覆盖');
        } else {
            foreach ($tables_conflict as $table) {
                $db->exec("DROP TABLE `$table`");
            }
        }
	}
	protected function initSql()
	{
        $sql_file = base_path() . '/plugin/admin/install.sql';
		static::ThrowOn( !is_file($sql_file),  '数据库SQL文件不存在');

        $sql_query = file_get_contents($sql_file);
		
        $sql_query = $this->removeComments($sql_query);
        $sql_query = $this->splitSqlFile($sql_query, ';');
		
        foreach ($sql_query as $sql) {
            $db->exec($sql);
        }
		
	}
	public function checkInstallLogFile()
	{
		return true;
	}
	public function step1($post)
    {
        $user = $post['user'];
        $password = $post['password'];
        $database = $post['database'];
        $host =  $post['host'];
        $port = $post['port'];
        $overwrite = $post['overwrite'];
		
		$flag = $this->checkInstallLogFile(); // 原版是锁文件
		static::ThrowOn($flag ,'管理后台已经安装！如需重新安装，请删除该插件数据库配置文件并重启');

        try {
            $db = App::G()-> foo(); // 这段是系统程序员的活了
            $smt = $db->query("show databases like '$database'");
            if (empty($smt->fetchAll())) {
                $db->exec("create database $database");// 安全问题
            }
            $db->exec("use $database");
            $smt = $db->query("show tables");
            $tables = $smt->fetchAll();
        } catch (\Throwable $e) {
			static::ThrowOn(stripos($e, 'Access denied for user'), '数据库用户名或密码错误');
			static::ThrowOn(stripos($e, 'Connection refused'), 'Connection refused. 请确认数据库IP端口是否正确，数据库已经启动');
			static::ThrowOn(stripos($e, 'timed out'), '数据库连接超时，请确认数据库IP端口是否正确，安全组及防火墙已经放行端口');
			throw $e;
        }
		$this->checkTableOverwrite();
		$this->initSql();
        
        // 导入菜单
        $menus = include base_path() . '/plugin/admin/config/menu.php';
        RuleModel::G()->importMenu($menus);
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
		static::ThrowOn($password != $password_confirm, '两次密码不一致');
        $flag = $this->checkDataBase();
		static::ThrowOn(!$flag, '请先完成第一步数据库配置');
        
		$flag = AdminRoleModel::G()->hasAdmins();
		static::ThrowOn($flag, '后台已经安装完毕，无法通过此页面创建管理员');
		
        $admin_id = AdminModel::G()->addFirstAdmin($username, $password);
		AdminRoleModel::G()->addFirstRole($admin_id);
    }
	protected function checkDatabase()
	{
		return !is_file($config_file = base_path() . '/plugin/admin/config/database.php');
	}

}