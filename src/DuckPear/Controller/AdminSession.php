<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckPear\ControllerEx;

use DuckPear\System\ProjectSession;
/**
 * 会话处理
 */
class AdminSession extends ProjectSession
{
    public function getCurrentAdmin()
    {
        $ret = $this->get('admin', []);
        static::ThrowOn(empty($ret), '请重新登录');
        return $ret;
    }
    public function getCurrentAdminId()
    {
        $user = $this->getCurrentAdmin();
        return $user['id'];
    }
    
    public function setCurrentAdmin($admin)
    {
        $this->set('admin', $admin);
    }
    public function logout()
    {
        $this->set('admin', []);
        $this->unset('admin');
    }
    public function getPhrase()
    {
        return $this->get('phrase', '');
    }
    public function setPhrase($phrase)
    {
        return $this->set('phrase', $phrase);
    }
    ////////////////////////////////////////////////////////////////////////
    public function csrf_token()
    {
        $token = $this->get('_token');
        if (!isset($token)) {
            $token = $this->randomString(40);
            $this->set('_token', $token);
        }
        return $this->get('_token');
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
