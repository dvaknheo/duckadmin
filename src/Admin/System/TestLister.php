<?php declare(strict_types=1);
namespace DuckAdmin\Admin\System;

use DuckPhp\Core\App;
use DuckPhp\Foundation\Helper;
use DuckPhp\Foundation\SingletonTrait;

/**
 * AdminApp 子应用 DuckCoverage 测试列表
 */
//@codeCoverageIgnoreStart
class TestLister
{
    use SingletonTrait;

    public const TEST_DB = 'admin-duckcoverage.db';
    public const ADMIN_NAME = 'admin';
    public const ADMIN_PASSWORD = '123456';

    public static function BeforeTest()
    {
        static::_()->_BeforeTest();
    }

    public function _BeforeTest()
    {
        @unlink(Helper::PathOfRuntime() . self::TEST_DB);
    }
    public static function GetTestListForLogin()
    {
        return static::_()->_GetTestListForLogin();
    }

    public function _GetTestListForLogin()
    {
        // 「只建立登录态」：不安装、不登出，因此可以被别的应用反复 embed。
        // 全部用相对 uri —— 当前 phase 就是正在取清单的那个管理员应用:
        // Admin / Admin_LaravelModel / Admin_ThinkPHPModel 共用这一份 lister,
        // phase 不同,__url() 会自动补成 /admin/ 或 /admin_laravel/ …（不用再算前缀）。
        $admin_name = self::ADMIN_NAME;
        $admin_password = self::ADMIN_PASSWORD;
        $list = <<<EOT
#PHASE_BEGIN
WEB login
WEB login username={$admin_name}&password={$admin_password}
#PHASE_END
EOT;
        return $list;
    }
    public static function GetTestListForLogout()
    {
        return static::_()->_GetTestListForLogout();
    }
    public function _GetTestListForLogout()
    {
        $list = <<<EOT
#PHASE_BEGIN
WEB logout
#PHASE_END
EOT;
        return $list;
    }
    public static function GetTestList()
    {
        return static::_()->_GetTestList();
    }

    public function _GetTestList()
    {
        // 用「当前 phase 的 App」(也就是正在取清单的那个子应用)来判断自己是不是被选中的提供者。
        // 不能用 AdminApp::_():在 Laravel/ThinkPHP 子应用的 phase 里它不存在于该 phase 的容器,
        // 会被新建出一个 AdminApp 实例、拿到类默认值(true),于是"非当前提供者"的清单也会被跑,
        // 那些请求实际由别的提供者代跑 —— 自己的适配器反而一行都覆盖不到。
        $flag = App::_()->options['admin_provider_enable'] ?? true;
        if(!$flag){
            return '';
        }
        // 被别的应用 embed 进来时，按调用方塞的一次性参数决定给哪种清单：
        //   short  = 只建立登录态（不安装、不登出，可重复嵌入）
        //   logout = 只登出
        // 本应用（管理员/用户系统之一）的清单类别：被别的应用嵌入时，按调用方塞的一次性参数决定。
        // 参数放在**根应用**上，读到就清，避免影响同一次 play 里后续的清单生成。
        $root = \DuckPhp\Core\App::Root();
        $cmd = (string)($root->options['duckcoverage_test_lister_parameter'] ?? '');
        if ($cmd !== '') {
            unset($root->options['duckcoverage_test_lister_parameter']);
        }
        switch ($cmd) {
            case 'login':
                return static::_()->_GetTestListForLogin();
            case 'logout':
                return static::_()->_GetTestListForLogout();
        }
        $id = \DuckAdmin\System\CoverageGroup::name();   // 组名统一走解析器,不猜文件
        @unlink("runtime/DuckCoverage/{$id}.db");
        $dbFile = \DuckAdmin\System\CoverageGroup::dbFile();
        $list = [];

        // ========== 初始化阶段（必须）==========
        $list[] = '#PHASE_BEGIN';
        $list[] = 'WEB install';
        // 先 GET 一次安装页(英文),覆盖 Admin 的 config/lang-en_US.php;
        // 文件名必须是 en_US 形状:头里的 en-US 会被 Lang 规范化成 en_US
        $list[] = "WEB install?lang=en-US";
        $list[] = "WEB install driver=sqlite&database[file]={$dbFile}&admin_name=" . self::ADMIN_NAME . "&admin_password=" . self::ADMIN_PASSWORD . "&admin_password_confirm=" . self::ADMIN_PASSWORD;
        $list[] = 'WEB login';
        $list[] = "WEB login username=" . self::ADMIN_NAME . "&password=" . self::ADMIN_PASSWORD;
        $list[] = 'WEB index';

        // ========== 登录失败分支 ==========
        // MainController::login() 自己 catch 后回显错误,所以这些不会变成 500、也不会脏日志;
        // 覆盖的是 AppBusiness::login() 里的几个 ThrowOn(空值/用户不存在/密码错/账号禁用)
        $list[] = 'WEB login username=&password=';
        $list[] = 'WEB login username=ghost_user&password=123456';
        $list[] = 'WEB login username=' . self::ADMIN_NAME . '&password=wrong_password';
        $list[] = 'WEB login username=disabled_user&password=123456';
        // 上面几次失败会把会话换掉,重新登录回超管再继续
        $list[] = 'WEB login username=' . self::ADMIN_NAME . '&password=' . self::ADMIN_PASSWORD;

        // ========== Home 模块 ==========
        $list[] = 'WEB Home/index';
        $list[] = 'WEB Home/profile';
        $list[] = 'WEB Home/profile realname=Admin&email=admin@test.com&password=';
        $list[] = 'WEB Home/profile password=123456';
        $list[] = 'WEB Home/permissions';
        $list[] = 'WEB Home/menu';

        // ========== Admin 模块 ==========
        $list[] = 'WEB Admin/index';
        $list[] = 'WEB Admin/index username=admin';
        $list[] = 'WEB Admin/create';
        $list[] = 'WEB Admin/save username=testuser1&password=123456&realname=TestUser1&email=test1@test.com&status=1&role_id=1';
        $list[] = 'WEB Admin/edit id=1';
        $list[] = 'WEB Admin/update id=1&username=admin&password=&realname=AdminUpdated&email=admin@updated.com&status=1&role_id=1';
        $list[] = 'WEB Admin/save username=deluser&password=123456&realname=DelUser&email=del@test.com&status=1&role_id=2';
        $list[] = 'WEB Admin/save username=disabled_user&password=123456&realname=Disabled&email=disabled@test.com&status=0&role_id=2';
        $list[] = 'WEB Admin/delete id=999';

        // ========== Role 模块 ==========
        $list[] = 'WEB Role/index';
        $list[] = 'WEB Role/index search=admin';
        $list[] = 'WEB Role/create';
        $list[] = 'WEB Role/save name=testrole&description=testrole&pid=0';
        $list[] = 'WEB Role/edit id=2';
        $list[] = 'WEB Role/update id=2&name=updatedrole&description=updated&pid=0';
        $list[] = 'WEB Role/delete id=999';

        // ========== Permission 模块 ==========
        $list[] = 'WEB Permission/index';
        $list[] = 'WEB Permission/index id=1';
        $list[] = 'WEB Permission/index id=2';

        // ========== System 模块 ==========
        $list[] = 'WEB System/menu';
        $list[] = 'WEB System/menu_scan';
        $list[] = 'WEB System/menu_create';
        $list[] = 'WEB System/menu_save name=testmenu&url=/test&type=1&parent_id=0&weight=0';
        $list[] = 'WEB System/menu_edit id=1';
        $list[] = 'WEB System/menu_update id=1&name=updatedmenu&url=/updated&type=1&parent_id=0&weight=0';
        $list[] = 'WEB System/menu_delete id=999';
        $list[] = 'WEB System/info';
        $list[] = 'WEB System/setting';
        $list[] = 'WEB System/log';

        // ========== 非超管路径 ==========
        // deluser 是普通角色(role_id=2):登录后访问页面会走 checkUserUrl() 里的非超管分支,
        // 首页/菜单还会走 menu() 里「按角色取菜单」的分支(超管走的是另一条)
        $list[] = 'WEB login username=deluser&password=123456';
        $list[] = 'WEB index';
        $list[] = 'WEB Home/index';
        $list[] = 'WEB Home/menu';
        $list[] = 'WEB Home/permissions';
        $list[] = 'WEB Role/index';
        $list[] = 'WEB Admin/index';
        $list[] = 'WEB System/info';

        // 登出:原来整份清单没有 logout，MainController::logout() 与
        // AuthAction::logout() 一直是「从未执行」。放在最后是安全的——
        // 此时会话里是 deluser，后面各块都会自己重新登录管理员。
        $list[] = 'WEB logout';

        $list[] = '#PHASE_END';

        $str = implode("\n", $list);

        $args = [
            'static' => static::class,
        ];
        $str = str_replace(array_map(fn($k) => '{' . $k . '}', array_keys($args)), array_values($args), $str);

        return $str;
    }
}//@codeCoverageIgnoreEnd
