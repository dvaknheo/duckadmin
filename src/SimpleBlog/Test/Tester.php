<?php
namespace SimpleBlog\Test;

use DuckPhp\Foundation\SimpleSingletonTrait;

class Tester
{
    use SimpleSingletonTrait;
    public function getTestList()
    {
        //#COMMAND FUNCTION_METHOD
        $list = <<<EOT
#WEB 
#WEB article/5
#WEB 
#WEB admin/index
#WEB admin/articles
#WEB admin/comments
#WEB admin/logs
#WEB admin/comments
app/admin/logout
#WEB admin/comments
EOT;
        $prefix = \SimpleBlog\System\SimpleBlogApp::_()->options['controller_url_prefix'];
        $list = str_replace('#WEB ',$prefix,$list);

        return $list;
    }
}