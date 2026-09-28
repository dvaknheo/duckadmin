<?php declare(strict_types=1);
/**
 * DuckAdmin DemoUsers - 预设用户系统
 * 不使用数据库、不使用安装系统;登录限定 demo_users 数组中的用户
 */
namespace DuckAdmin\DemoUsers\System;

use DuckPhp\Foundation\SingletonTrait;


class TestLister
{
    use SingletonTrait;
    public static function GetTestOrderList(): string
    {
        return static::_()->_GetTestOrderList();
    }
    public static function _GetTestOrderList(): string
    {
        $list = '';
        return $list;
    }
    public function getTestList()
    {
        $list = <<<EOT
#WEB 
#WEB index
#ADMINWEB account/login username=admin&password=123456&captcha=7268
#USERWEB login name={username}&password=123456&password_confirm=123456

#WEB admin/article_add
#WEB admin/article_add title=tttttt&content=ccccccccccc
#WEB article/{new_article_id}
#WEB article/9999999
#WEB addcomment
#WEB addcomment article_id={new_article_id}&content=aaaaaaaaa
#WEB delcomment
#WEB delcomment id={new_comment_id}
#WEB addcomment article_id={new_article_id}&content=bbbbbbbbbb
#WEB article/{new_article_id}

#WEB admin/article_edit?id={new_article_id}
#WEB admin/article_edit id={new_article_id}&title=xxxxxxx&content=zzzzzzzzz
#WEB admin/article_delete
#WEB admin/article_delete id={new_article_id}
#WEB admin/delete_comments id={new_comment_id2}

#WEB admin/index
#WEB admin/articles
#WEB admin/comments

#SETWEB _ _ {static}@testControllerHelper
#WEB index
EOT;
        $prefix = SimpleBlogApp::_()->options['controller_url_prefix'];
        $new_article_id = $this->getNextInsertId(ArticleModel::_()->table());
        $new_comment_id = $this->getNextInsertId(CommentModel::_()->table());
        $args = [
            'username'  => 'user_test1',
            'new_article_id' => $new_article_id,
            'new_comment_id' => $new_comment_id,
            'new_comment_id2' => $new_comment_id+1,
        ];
        $args ['static'] = static::class;
        $list = $this->replace_string($list,$args);
        
        $list = str_replace('#WEB ','#WEB '.$prefix,$list);
        $admin_prefix = 'app/admin/';
        $list = str_replace('#ADMINWEB ','#WEB '.$admin_prefix,$list);
        
        $user_prefix = 'user/';
        $list = str_replace('#USERWEB ','#WEB '.$user_prefix,$list);
        
        return $list;
    }

}