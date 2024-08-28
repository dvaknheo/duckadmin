<?php
namespace DuckUserManager\Test;

use DuckPhp\Foundation\SimpleSingletonTrait;

class Tester
{
    use SimpleSingletonTrait;
    public function setHash()
    {
        if(isset($_GET['id'])&&  $_GET['id'] ==='{id}'){
            $user_id = $this->getIdFromUser('ttt1');
            $_GET['id'] = $user_id;
            if(isset($_GET['hash'])){
                @session_start();
                $hash_id = $_SESSION['hash']??null;
                $_GET['hash'] = md5($hash_id.'|'.$user_id);
            }
        }
    }
    public function getTestList()
    {
        $list = <<<EOT
#XWEB account/login username=admin&password=123456&captcha=7268
## 注册用户
#YWEB register name=ttt1&password=123456&password_confirm=123456
#WEB user/index
#WEB user/index?is_all=1
#SETWEB _ {static}@setHash _ _
#WEB user/delete?id={id}&hash=_hash
#SETWEB _ {static}@setHash _ _
#WEB user/undelete?id={id}&hash=_hash
EOT;
        $prefix = \DuckUserManager\System\DuckUserManagerApp::_()->options['controller_url_prefix'];
        $list = str_replace('#WEB ','#WEB '.$prefix,$list);
        $list = str_replace('{static}',static::class,$list);
        $list = str_replace('#XWEB ','#WEB /app/admin/',$list);
        $list = str_replace('#YWEB ','#WEB /user/',$list);

        return $list;
    }
    protected function getIdFromUser($name)
    {
        $sql = "select id from Users where username = ?";
        $ret = \DuckPhp\Component\DbManager::Db()->fetchColumn($sql,$name);
        return $ret;
        
    }
}