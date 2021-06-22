<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckAdmin\Business;

use DuckAdmin\System\App as App;

// 特殊业务， 会话
class SessionBusiness extends Base
{
    protected $prefix = 'duckadmin_';
    public function __construct()
    {
        App::session_start();
    }
    public function getCurrentAdmin()
    {
        $ret = App::SessionGet($this->prefix.'admin', []);
        BusinessException::ThrowOn(empty($ret), '请重新登录');
        
        return $ret;
    }
    
    public function getCurrentAdminId()
    {
        $user = $this->getCurrentAdmin();
        return $user['id'];
    }
    
    public function setCurrentAdmin($admin)
    {
        App::SessionSet($this->prefix.'admin', $admin);
    }
    public function logout()
    {
        App::SessionSet($this->prefix.'admin', []);
        unset($_SESSION['admin']);
        //App::session_destroy();
    }
    public function getPhrase()
    {
        return App::SessionGet($this->prefix.'phrase', '');
    }
    public function setPhrase($phrase)
    {
        return App::SessionSet($this->prefix.'phrase', $phrase);
    }
    ////////////////////////////////////////////////////////////////////////
    public function csrf_token()
    {
        $token = App::SessionGet($this->prefix.'_token');
        if (!isset($token)) {
            $token = $this->randomString(40);
            App::SessionSet($this->prefix.'_token', $token);
        }
        return App::SessionGet($this->prefix.'_token');
    }
    protected function randomString($length = 16)
    {
        $string = '';
        while (($len = strlen($string)) < $length) {
            $size = $length - $len;
            $bytes = random_bytes($size);
            $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }

        return $string;
    }
}
