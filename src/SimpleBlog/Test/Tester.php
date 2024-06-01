<?php
namespace SimpleBlog\Test;

use DuckPhp\Foundation\SimpleSingletonTrait;
use SimpleBlog\System\SimpleBlogApp;
use DuckPhp\Foundation\Helper;
class Tester
{
    use SimpleSingletonTrait;
    public function getTestList()
    {
        $list = <<<EOT
#WEB 
#WEB index
#WEB article/2
#WEB article/9999999
#ADMINWEB account/login username=admin&password=123456&captcha=7268
#USERWEB login name={username}&password=123456&password_confirm=123456
#WEB addcomment
#WEB addcomment article_id=2&content=ccccc
#WEB delcomment
#WEB delcomment id=9

#WEB admin/article_add
#WEB admin/article_add title=tttttt&content=ccccccccccc
#WEB admin/article_edit?id={new_article_id}
#WEB admin/article_edit id={new_article_id}&title=xxxxxxx&content=zzzzzzzzz
#WEB admin/article_delete id={new_article_id}
#WEB admin/delete_comments id={comment_id2}

#WEB admin/index
#WEB admin/articles
#WEB admin/article_add

#WEB admin/comments
#WEB admin/logs
#WEB admin/comments
#ADMINWEB app/admin/account/logout
#WEB admin/comments

EOT;
        $prefix = SimpleBlogApp::_()->options['controller_url_prefix'];
        $args = [
            'username' =>'user_test1',
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