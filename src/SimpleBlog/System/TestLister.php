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
        // 管理端 / 用户端各自的「只登录」清单：由根应用顶层预取(CoverageGroup::primeShortLists)，
        // 这里只做纯文本内联 —— 子应用的 lister 里切 phase 是无效的,而且 #INCLUDE_CHILD
        // 是递归展开、没有环检测,菱形引用会爆栈。
        $admin_app = \DuckAdmin\System\CoverageGroup::adminProvider();
        $user_app  = \DuckAdmin\System\CoverageGroup::userProvider();
        $here = \DuckPhp\Core\App::Phase();
        $admin_short = \DuckAdmin\System\CoverageGroup::shortInline($admin_app, $here);
        $user_short  = \DuckAdmin\System\CoverageGroup::shortInline($user_app, $here);

        $list = <<<EOT
#PHASE_BEGIN
COMMENT 先 GET 一次安装页(用 ?lang=en-US)覆盖 config/lang-en_US.php ——
COMMENT 博客的普通页面文案是硬编码中文、不调 __l(),只有安装页才会加载语言包
WEB install?lang=en-US
WEB install driver=sqlite&database%5Bfile%5D={db}&database%5Bhost%5D=127.0.0.1&database%5Bport%5D=&database%5Bdbname%5D=&database%5Busername%5D=&database%5Bpassword%5D=&action=install
{$admin_short}
{$user_short}

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
WEB article/1?lang=en-US
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

COMMENT userController(继承 UserControllerBase,需要用户登录态)
COMMENT 登录态要"紧挨着"用:中间夹了十几个 admin 步骤后 session 会不在,
COMMENT 所以这里再内联一次「用户提供者的只登录清单」(幂等,代价 1~2 个请求)
{$user_short}
WEB user/index
#PHASE_END
EOT;

        return $this->replace_string($list, [
            // 上游公开的方法是 watchingGetName()（不是 watchingName()）；
            // 写错名字会让整份清单生成直接抛 "Call to undefined method"，
            // 结果是所有组都回放 0 个请求（实测踩过）。
            'db' => \DuckAdmin\System\CoverageGroup::dbFile(),
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
