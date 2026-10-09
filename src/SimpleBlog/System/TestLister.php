<?php declare(strict_types=1);
/**
 * DuckAdmin SimpleBlog - DuckCoverage 测试列表
 */
namespace DuckAdmin\SimpleBlog\System;

use DuckAdmin\System\TestListHelper;
use DuckPhp\Core\App;
use DuckPhp\Foundation\SingletonTrait;

//@codeCoverageIgnoreStart
class TestLister
{
    use SingletonTrait;
    public function cleanInstall()
    {
        return TestListHelper::_()->cleanInstall();
    }
    public static function GetTestList(): string
    {
        return static::_()->_GetTestList();
    }
    public function _GetTestList(): string
    {
        $phase = App::Phase();
        $dbfile = TestListHelper::_()->getTestDbFile('simpleblog');

        $list = <<<EOT
PHASE {$phase}
WEB install
WEB install driver=sqlite&database%5Bfile%5D={$dbfile}&database%5Bhost%5D=127.0.0.1&database%5Bport%5D=&database%5Bdbname%5D=&database%5Busername%5D=&database%5Bpassword%5D=&action=install

COMMENT Admin login
#ADMIN_LOGIN
PHASE {$phase}
COMMENT User login

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

#USER_LOGIN
PHASE {$phase}
WEB user/index

EOT;
        return $list;
    }

}//@codeCoverageIgnoreEnd
