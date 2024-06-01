<?php
namespace SimpleBlog\Test;

use DuckPhp\Foundation\SimpleSingletonTrait;
use DuckPhp\Foundation\Helper;
use SimpleBlog\System\SimpleBlogApp;
use SimpleBlog\Model\ArticleModel;
use SimpleBlog\Model\CommentModel;
use SimpleBlog\Controller\Helper as ControllerHelper;
class Tester
{
    use SimpleSingletonTrait;
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
    public function testControllerHelper()
    {
        ControllerHelper::_()->recordsetUrl([],[]);
        ControllerHelper::_()->recordsetUrl([[]],[]);
        ControllerHelper::_()->recordsetH([['encode'=>'<>']],[]);
    }
    private function getNextInsertId($table)
    {
        $sql = "select seq from sqlite_sequence where name = ?";
        $ret = Helper::Db()->fetchColumn($sql,$table);
        $ret =(int)$ret+1;
        return $ret;
        
    }
    private function replace_string($str,$args = [])
    {
        if (empty($args)) {
            return $str;
        }
        $a = [];
        foreach ($args as $k => $v) {
            $a["{".$k."}"] = $v;
        }
        
        $ret = str_replace(array_keys($a), array_values($a), $str);
        
        return $ret;
    }
}