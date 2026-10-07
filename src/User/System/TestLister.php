<?php declare(strict_types=1);
namespace DuckAdmin\User\System;

use DuckPhp\Core\App;
use DuckPhp\Foundation\SingletonTrait;

//@codeCoverageIgnoreStart
class TestLister
{
    use SingletonTrait;
    public static function GetTestList(): string
    {
        $out = static::_()->_GetTestListFromCategory();
        // 统一在这里展开 phase 行：调用方（别的应用）会先切到**我们的** phase 再调我们，
        // 于是 #PHASE_BEGIN/#PHASE_END 会展开成"我们 → 调用方"的正确 PHASE 行；
        // 展开后已无 # 行，duckcoverage 之后再展开一次也是空操作。
        return $out === '' ? '' : \DuckCoverage\TestListerHelper::_()->explainMarco($out);
    }
    public static function GetTestListForLogin(): string
    {
        return static::_()->_GetTestListForLogin();
    }
    public function _GetTestListForLogin(): string
    {
        // 「只建立登录态」：不安装、不登出，所以可以被别的应用反复 embed。
        // 全部相对 uri —— 当前 phase 就是 UserApp,__url() 自动补成 /user/。
        // register 的幂等性：已存在时会失败(不脏日志),随后的 login 依然成功。
        $list = <<<EOT
#PHASE_BEGIN
WEB register name=t1&password=123456&password_confirm=123456
WEB login name=t1&password=123456
#PHASE_END
EOT;
        return $list;
    }
    public static function GetTestListForLogout(): string
    {
        return static::_()->_GetTestListForLogout();
    }
    public function _GetTestListForLogout(): string
    {
        $list = <<<EOT
#PHASE_BEGIN
WEB logout
#PHASE_END
EOT;
        return $list;
    }
    public static function _GetTestListFromCategory(): string
    {
        $flag = App::_()->options['user_provider_enable'] ?? true;
        if(!$flag){
            return '';
        }
        // 被别的应用 embed 时,按调用方塞的一次性参数决定给哪种清单
        // 本应用（管理员/用户系统之一）的清单类别：被别的应用嵌入时，按调用方塞的一次性参数决定。
        // 参数放在**根应用**上，读到就清，避免影响同一次 play 里后续的清单生成。
        $root = App::_();   // 参数就放在"正在被问的那个应用"身上
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
        $list_referenct = <<<EOT
WEB users/Home/index
WEB users/
WEB users/logout
UserBusiness UserBusiness@canAccess user_id=&class=&method=&url=
CALL DuckAdmin\DemoUsers\Business\UserBusiness@log user_id=&string=&type=&ext=
CALL DuckAdmin\DemoUsers\Business\UserBusiness@batchGetUsernames ids=
CALL DuckAdmin\DemoUsers\Business\UserBusiness@login post=

EOT;

        // 问“当前正在服务的”管理员系统要登录清单（通用取法,不需要知道它是谁）。
        // 用户管理页需要管理员登录态,而管理员态可能在用户侧操作后被清掉,所以这里重登一次。
        $admin_login = \DuckCoverage\TestListerHelper::TestListByAdminLogin();

        $list = <<<EOT
#PHASE_BEGIN
CALL {static}@beginTest
COMMENT 先 GET 一次安装页,渲染「表单分支」;再 POST 安装,渲染「完成页分支」。
COMMENT 只做 POST 的话 view/install.php 的表单分支永远覆盖不到(实测 31%)
WEB install
WEB install driver=sqlite&database%5Bfile%5D={db}&database%5Bhost%5D=127.0.0.1&database%5Bport%5D=&database%5Bdbname%5D=&database%5Busername%5D=&database%5Bpassword%5D=&action=install

COMMENT 注册:表单 + 成功 + 重名 + 两次密码不一致
WEB register
WEB register name=t1&password=123456&password_confirm=123456
WEB register name=t1&password=123456&password_confirm=123456
WEB register name=t2&password=123456&password_confirm=999999

COMMENT 登录:表单 + 错密码 + 正确
WEB login
WEB login name=t1&password=nollllllllllllllllllllog
WEB login name=t1&password=123456
WEB index

COMMENT 登录后的主页与改密码页(HomeController 需要登录态)
COMMENT 注意:MainController 没有 password action,要用 Home/password
WEB Home/index
WEB Home/password
WEB Home/password oldpassword=123456&newpassword=654321&newpassword_confirm=654321
WEB Home/password oldpassword=654321&newpassword=123456&newpassword_confirm=123456
WEB Home/password oldpassword=123456&newpassword=123456&newpassword_confirm=999999

COMMENT 用户管理(必须放在用户登出之前:登出会把整个 session 清掉,
COMMENT 管理员登录态也会一起没,AdminControllerBase 就会直接 302 出去,
COMMENT 于是 AdminController 与 view/Admin/* 永远是 0%)
COMMENT 管理员登录态由同一组的 Admin 块负责建立
COMMENT 管理员登录态可能已被用户侧的操作清掉:这里用绝对 uri 重新登录一次
{$admin_login}
COMMENT 重要:AdminController 用 Helper::GET 读参数,所以查询串必须写在 uri 上。
COMMENT "WEB Admin/index all=1" 这种参数是 POST 体,控制器读不到 —— 等于没测。
WEB Admin/index
WEB Admin/index?all=1
COMMENT 删除/还原现在真的校验 hash(md5(会话里的 hash|id))。
COMMENT 合法 hash 取决于会话里的随机值,清单这边预制不了(SETWEB 钩子在回放进程里跑,
COMMENT 碰不到应用会话),所以这里只保留负例:错的 hash 必须被拒绝。
COMMENT 成功分支(302 + 真删除)已用 curl 手工验证过:带正确 hash → 302 且软删除生效。
WEB Admin/delete?id=2&hash=deadbeefdeadbeefdeadbeefdeadbeef
WEB Admin/index?all=1
WEB Admin/index

COMMENT 英文语言包:不带 ?lang=en-US 时 config/lang-en_US.php 永远不加载,一直是 0%
COMMENT HTTP 表达不到的业务/模型层交给 SETWEB 后置钩子(见下面的 moreTest)
SETWEB _ _ {phase}!{static}@moreTest _
WEB login?lang=en-US
WEB Home/index?lang=en-US
WEB logout

CALL {static}@endTest
#PHASE_END

EOT;
        $file = UserApp::_()->getRuntimePath() .'DuckCoverage/DuckCoverage.watching.txt';
        $watch_name = \DuckAdmin\System\CoverageGroup::name();   // 组名统一走解析器,不猜文件
        $db = "runtime/DuckCoverage/{$watch_name}.db";

        $list = str_replace('{db}', $db, $list);
        $list = str_replace('{static}', static::class, $list);
        $list = str_replace('{phase}', UserApp::Phase(), $list);

        return $list;
    }
    public function beginTest()
    {
        
        //我们要删除旧测试数据库文件
    }
    public function endTest()
    {
        //我们要删除旧测试数据库文件
    }

    /**
     * SETWEB 后置钩子：在每个 web 调用之后触发，用来覆盖 HTTP 表达不到的
     * 业务层/模型层方法。
     *
     * 为什么必须在这里调、而不是用 #BUSINESS/#MODEL 宏展开成 CALL：
     * 宏展开是 `CALL :DuckUser!DuckAdmin\User\Business\...`，在 CLI 里直接执行，
     * 那时子应用的 database_list 还没有生效，模型层一取数据库就抛
     * "setting database_list[0] missing" 并中断整场 play（实测过）。
     * 钩子是在该子应用的真实请求里跑的，配置已生效 —— 与 DemoUsers 的
     * TestLister::moreTest2() 同一套路。
     *
     * 每步各自 try：任何一个失败都不影响其它步，也不会中断 play；
     * static 守卫让这一批调用只跑一次（钩子本身会随每个 web 调用触发）。
     */
    public function moreTest()
    {
        // 注意：本钩子是 SETWEB 的 post_webcall，它在**回放进程（CLI）**里执行，
        // 不在被测应用的请求里 —— 所以这里写 $_SESSION 对应用的会话没有任何影响
        // （实测：写过也读不到）。删除/还原要用的合法 hash 因此没法在这里"预制"，
        // 清单里只保留了负例（错 hash → 校验必须拒绝）。见下面 Admin 那几步的注释。
        static $done = false;
        if ($done) {
            return;
        }
        $done = true;

        $calls = [
            // UserAdminBusiness / UserAdminModel：整文件原来 0%
            ['\DuckAdmin\User\Business\UserAdminBusiness', 'getUserList', [[], 1, 10]],
            ['\DuckAdmin\User\Business\UserAdminBusiness', 'getUserList', [['all' => 1], 1, 10]],
            ['\DuckAdmin\User\Business\UserAdminBusiness', 'deleteUser', [1, 2]],
            ['\DuckAdmin\User\Business\UserAdminBusiness', 'unDeleteUser', [1, 2]],
            ['\DuckAdmin\User\Model\UserAdminModel', 'getUserList', [[], 1, 5]],
            ['\DuckAdmin\User\Model\UserAdminModel', 'getUserList', [['all' => 1], 1, 5]],
            ['\DuckAdmin\User\Model\UserAdminModel', 'deleteUser', [2]],
            ['\DuckAdmin\User\Model\UserAdminModel', 'unDeleteUser', [2]],
            // UserModel / UserBusiness 剩下的分支
            ['\DuckAdmin\User\Model\UserModel', 'getUserById', [1]],
            ['\DuckAdmin\User\Model\UserModel', 'getUserByUsername', ['t1']],
            ['\DuckAdmin\User\Model\UserModel', 'exsits', ['nobody_xyz']],
            ['\DuckAdmin\User\Model\UserModel', 'addUser', ['t9', '123456']],
            ['\DuckAdmin\User\Model\UserModel', 'updatePassword', [1, '123456']],
            ['\DuckAdmin\User\Model\UserModel', 'batchGetUsernames', [[1, 2]]],
            ['\DuckAdmin\User\Business\UserBusiness', 'batchGetUsernames', [[1, 2]]],
            ['\DuckAdmin\User\Business\UserBusiness', 'changePassword', [1, '123456', '123456']],
            ['\DuckAdmin\User\Business\UserBusiness', 'log', [1, 'coverage', 'test']],
            // 本应用 Controller\Helper 的两个 csrf 包装(控制器目前直接调 Session::_(),
            // 所以它们没被走到)。注意只能用实例方式调:应用自己的 Helper 是普通子类,
            // 它自己新增的方法不是 static,写成 Helper::csrfToken() 会
            // "cannot be called statically"。
            ['\DuckAdmin\User\Controller\Helper', 'csrfToken', []],
            ['\DuckAdmin\User\Controller\Helper', 'csrfField', []],
            ['\DuckAdmin\User\Controller\Helper', 'checkCsrf', []],
        ];
        foreach ($calls as [$class, $method, $args]) {
            try {
                $class::_()->$method(...$args);
            } catch (\Throwable $ex) {
                // 这里就是来踩边界的：失败无所谓，不能让它中断整场 play
            }
        }
    }

}
/*
// 首先，我们要搞安装系统
我们从注册登录 安装开始

$list = <<<EOT
COMMENT tests for x
WEB index
WEB register
WEB register name={username}&password=123456&password_confirm=123456
WEB register name={username}&ssssssssssssssssssssssamename=1
WEB Home/index?_r=1
WEB logout
WEB index
WEB login
WEB login name={username}&password=nolllllllllllllllllllogin
WEB login name={username}&password=123456
WEB Home/index?_r=2
WEB Home/password
WEB Home/password oldpassword=123456&newpassword=654321&newpassword_confirm=654321
WEB Home/password oldpassword=654321&newpassword=123456&newpassword_confirm=123456
WEB Home/password oldpassword=654321&newpassword=123456&newpassword_confirm=123456
EOT;
$list = <<<EOT
PHASE {phase}
WEB register
EOT;


RUN DuckUser:version
RUN DuckUser:help
RUN DuckUser:run
RUN DuckUser:fetch
RUN DuckUser:call
RUN DuckUser:routes
RUN DuckUser:debug
WEB user/Admin/index
WEB user/Admin/delete
WEB user/Admin/undelete
WEB user/Home/index
WEB user/Home/password
WEB user/
WEB user/register
WEB user/login
WEB user/logout
CALL DuckAdmin\User\Business\UserAdminBusiness@getUserList conditions=&page=1&page_size=10
CALL DuckAdmin\User\Business\UserAdminBusiness@deleteUser admin_id=&id=
CALL DuckAdmin\User\Business\UserAdminBusiness@unDeleteUser admin_id=&id=
CALL DuckAdmin\User\Business\UserBusiness@log user_id=&string=&type=
CALL DuckAdmin\User\Business\UserBusiness@checkAccess id=&class=&method=&url=
CALL DuckAdmin\User\Business\UserBusiness@register form=
CALL DuckAdmin\User\Business\UserBusiness@login form=
CALL DuckAdmin\User\Business\UserBusiness@changePassword uid=&password=&new_password=
CALL DuckAdmin\User\Business\UserBusiness@batchGetUsernames ids=
CALL DuckAdmin\User\Business\UserBusiness@getUserList
CALL DuckAdmin\User\Model\UserAdminModel@getUserList where=&page=1&page_size=10
CALL DuckAdmin\User\Model\UserAdminModel@deleteUser id=
CALL DuckAdmin\User\Model\UserAdminModel@unDeleteUser id=
CALL DuckAdmin\User\Model\UserModel@init
CALL DuckAdmin\User\Model\UserModel@exsits name=
CALL DuckAdmin\User\Model\UserModel@addUser username=&password=
CALL DuckAdmin\User\Model\UserModel@getUserById id=
CALL DuckAdmin\User\Model\UserModel@getUserByUsername username=
CALL DuckAdmin\User\Model\UserModel@batchGetUsernames user_ids=
CALL DuckAdmin\User\Model\UserModel@verifyPassword user=&password=
CALL DuckAdmin\User\Model\UserModel@unloadPassword user=
CALL DuckAdmin\User\Model\UserModel@updatePassword uid=&password=
*/
//@codeCoverageIgnoreEnd
