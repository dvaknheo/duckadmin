<?php
namespace SimpleBlog\Test;

use DuckPhp\Foundation\SimpleSingletonTrait;
use SimpleBlog\System\SimpleBlogApp;

class Tester
{
    use SimpleSingletonTrait;
    public function getTestList()
    {
        //#COMMAND FUNCTION_METHOD
        $list = <<<EOT
#WEB 
#WEB article/2
#WEB article/9999999
#WEB admin/index
#WEB admin/articles
#WEB admin/comments
#WEB admin/logs
#WEB admin/comments
app/admin/logout
#WEB admin/comments
EOT;
        $prefix = SimpleBlogApp::_()->options['controller_url_prefix'];
        $list = str_replace('#WEB ','#WEB '.$prefix,$list);
        
        return $list;
    }
}