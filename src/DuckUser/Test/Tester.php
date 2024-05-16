<?php
namespace DuckUser\Test;

use DuckPhp\Foundation\SimpleSingletonTrait;

class Tester
{
    use SimpleSingletonTrait;
    public function getTestList()
    {
        //#COMMAND FUNCTION_METHOD
        $list = <<<EOT
#WEB 
#WEB register
#POST _token=vIAAsYGnBQAaXfzcP4tQy6Pgp1EuRNdizhEr4Rs3&name=aa1&password=123456&password_confirm=123456
#WEB register
#WEB Home/index
#WEB logout
#WEB index
#WEB login
#POST name=aa1&password=123456
#WEB login
#WEB Home/index
#WEB Home/password
#WEB #WEB Home/password oldpassword=123456&newpassword=654321&newpassword_confirm=654321

EOT;
        $prefix = \DuckUser\System\DuckUserApp::_()->options['controller_url_prefix'];
        $list = str_replace('#WEB ',$prefix,$list);
        return $list;
    }
}
//    public function removeDirectoryFromWhitelist(string $directory, string $suffix = '.php', string $prefix = ''): void
//    public function removeFileFromWhitelist(string $filename): void
