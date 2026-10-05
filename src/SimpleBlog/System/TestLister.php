<?php declare(strict_types=1);
/**
 * DuckAdmin SimpleBlog - DuckCoverage 测试列表
 *
 * 注意(踩过的坑):
 * 1) 本方法在「清单生成时」被调用,那时本应用可能还没安装,
 *    所以不能在这里访问数据库取 id —— 占位 id 一律写死,每组用全新 DB,install 后 id 从 1 开始。
 * 2) 本应用自己的路由用「相对 uri」:__url() 会按当前 phase 的 controller_url_prefix 补上 /blog/。
 *    带上 'blog/' 反而会变成 /blog/blog/...。
 * 3) 其他应用(管理端登录、用户端注册登录)的路由要用「以 / 开头的绝对 uri」,
 *    否则会被拼成 /blog/admin/login。
 * 4) 命令行前缀用当前的 "WEB",带 '#' 的写法是已废弃的旧 DSL。
 */
namespace DuckAdmin\SimpleBlog\System;

use DuckPhp\Foundation\SingletonTrait;
use DuckCoverage\DuckCoverage;
//@codeCoverageIgnoreStart
class TestLister
{
    use SingletonTrait;

    public static function GetTestList(): string
    {
        return static::_()->_GetTestList();
    }

    public function _GetTestList(): string
    {
        // 管理员前缀要与首页所选中的「管理员提供者」一致,否则登录态不共享
        $admin_prefix = '/admin/';

        // 用户端注册/登录只在「用户提供者 = UserApp」时才做:
        // 换成 DemoUsers 时它有自己的预设账号(由 DemoUsers 那一块负责登录),
        // 这时候再去 /user/register 只会白拿一个失败响应。
        $user_provider = (string)(\DuckPhp\Core\App::Root()->options['data']['duckadmin_user_provider'] ?? '');
        $user_steps = ($user_provider === \DuckAdmin\User\System\UserApp::class)
            ? "WEB /user/register name={username}&password=123456&password_confirm=123456\nWEB /user/login name={username}&password=123456"
            : "COMMENT 用户提供者不是 UserApp,跳过注册/登录,沿用该提供者已有的登录态";

        $list = <<<EOT
#PHASE_BEGIN
COMMENT 先 GET 一次安装页(用 ?lang=en)覆盖 config/lang-en.php ——
COMMENT 博客的普通页面文案是硬编码中文、不调 __l(),只有安装页才会加载语言包
WEB install?lang=en
WEB install driver=sqlite&database%5Bfile%5D={db}&database%5Bhost%5D=127.0.0.1&database%5Bport%5D=&database%5Bdbname%5D=&database%5Busername%5D=&database%5Bpassword%5D=&action=install
WEB {admin_prefix}login username=admin&password=123456
{user_steps}

COMMENT 后台:先造数据(每组 DB 全新,id 从 1 开始)
WEB admin/index
WEB admin/articles
WEB admin/article_add
WEB admin/article_add title=tttttt&content=ccccccccccc
WEB admin/articles
WEB admin/article_edit?id=1
WEB admin/article_edit id=1&title=xxxxxxx&content=zzzzzzzzz

COMMENT 前台
WEB
WEB article/1
WEB article/1?lang=en
WEB article/9999999
WEB addcomment article_id=1&content=aaaaaaaaa
WEB article/1
COMMENT delcomment 是 POST,按 id 删
WEB delcomment id=1
WEB article/1
WEB addcomment article_id=1&content=bbbbbbbbbb

COMMENT addcomment / delcomment 的「非 POST 早退」分支(用 GET 撞一下,不会真删改)
WEB addcomment
WEB delcomment

COMMENT 评论后台
WEB admin/comments
WEB admin/delete_comments id=2
WEB admin/comments

COMMENT 收尾:删除文章
COMMENT article_delete 的「非 POST 早退」分支(先 GET 撞一次)
WEB admin/article_delete
WEB admin/article_delete id=1
WEB admin/articles

COMMENT userController(继承 UserControllerBase,需要用户登录态;
COMMENT 博客里没人访问它,所以一直是 0%)
WEB user/index
#PHASE_END
EOT;

        return $this->replace_string($list, [
            'admin_prefix' => $admin_prefix,
            'user_steps'   => $user_steps,
            'username'     => 'user_test1',
            'db'           => urlencode('runtime/DuckCoverage/' . DuckCoverage::_()->watchingName() . '.db'),
        ]);
    }

    /**
     * 把 {key} 占位符替换成对应值
     * @param array<string, mixed> $args
     */
    protected function replace_string(string $str, array $args): string
    {
        if (empty($args)) {
            return $str;
        }
        $map = [];
        foreach ($args as $k => $v) {
            $map['{' . $k . '}'] = (string)$v;
        }
        return str_replace(array_keys($map), array_values($map), $str);
    }
}//@codeCoverageIgnoreEnd
