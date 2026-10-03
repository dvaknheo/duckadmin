<?php declare(strict_types=1);
/**
 * DuckAdmin SimpleBlog - DuckCoverage 测试列表
 *
 * 修复记录:
 * 1) 原来 namespace 写成了 DuckAdmin\DemoUsers\System(从 DemoUsers 复制过来忘了改),
 *    与 src/DemoUsers/System/TestLister.php 撞类名 —— 一旦被 composer -o 建类映射或反射加载就致命。
 * 2) 命令行前缀从 "#WEB" 改成 "WEB":现在 duckcoverage 的 readCommand() 是按
 *    explain{Ucfirst(cmd)} 分发的,带 # 的 cmd 会变成 explain#web 并打印 Bad Request。
 *    (#PHASE_BEGIN/#PHASE_END/#BUSINESS/#MODEL/#ACTION 仍然带 #,它们由 explainMarco 预处理)
 * 3) 去掉了已不被 duckcoverage 支持的 "#SETWEB ... testControllerHelper"(该方法也不存在)。
 * 4) 补齐原来缺失的 replace_string() / getNextInsertId();方法名统一成本工程的
 *    duckcoverage_test_lister 约定:静态 GetTestList()。
 *
 * 注意:本类目前没有在 SimpleBlogApp 的 options 里登记(duckcoverage_test_lister),
 *      需要跑覆盖率时再挂上。
 */
namespace DuckAdmin\SimpleBlog\System;

use DuckAdmin\SimpleBlog\Model\ArticleModel;
use DuckAdmin\SimpleBlog\Model\CommentModel;
use DuckPhp\Component\DbManager;
use DuckPhp\Foundation\SingletonTrait;

class TestLister
{
    use SingletonTrait;

    public static function GetTestList(): string
    {
        return static::_()->_GetTestList();
    }

    public function _GetTestList(): string
    {
        // 本应用前缀(blog/)
        $prefix = (string)(SimpleBlogApp::_()->options['controller_url_prefix'] ?? '');
        // 下列前缀要和首页所选中的“提供者”一致,否则登录态不共享
        $admin_prefix = 'admin/';
        $user_prefix  = 'user/';

        $list = <<<EOT
#PHASE_BEGIN
# 需要管理员登录态,blog/admin/* 由 AdminControllerBase 鉴权
WEB {admin_prefix}login username=admin&password=123456
# 需要用户登录态,addcomment/delcomment 会取当前用户
WEB {user_prefix}register name={username}&password=123456&password_confirm=123456
WEB {user_prefix}login name={username}&password=123456

# 前台
WEB {prefix}
WEB {prefix}article/{new_article_id}
WEB {prefix}article/9999999
WEB {prefix}addcomment article_id={new_article_id}&content=aaaaaaaaa
# delcomment 是 POST
WEB {prefix}delcomment id={new_comment_id}
WEB {prefix}addcomment article_id={new_article_id}&content=bbbbbbbbbb
WEB {prefix}article/{new_article_id}

# 后台
WEB {prefix}admin/index
WEB {prefix}admin/articles
WEB {prefix}admin/article_add
WEB {prefix}admin/article_add title=tttttt&content=ccccccccccc
WEB {prefix}admin/article_edit?id={new_article_id}
WEB {prefix}admin/article_edit id={new_article_id}&title=xxxxxxx&content=zzzzzzzzz
WEB {prefix}admin/article_delete id={new_article_id}
WEB {prefix}admin/comments
WEB {prefix}admin/delete_comments id={new_comment_id}
#PHASE_END
EOT;

        $args = [
            'prefix'         => $prefix,
            'admin_prefix'   => $admin_prefix,
            'user_prefix'    => $user_prefix,
            'username'       => 'user_test1',
            'new_article_id' => $this->getNextInsertId(ArticleModel::_()->table()),
            'new_comment_id' => $this->getNextInsertId(CommentModel::_()->table()),
        ];

        return $this->replace_string($list, $args);
    }

    /**
     * 取某表下一个自增 id(仅用于测试数据占位)
     */
    protected function getNextInsertId(string $table): int
    {
        $row = DbManager::Db()->fetch('SELECT COALESCE(MAX(id), 0) + 1 AS next_id FROM ' . $table);
        return (int)($row['next_id'] ?? 1);
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
}
