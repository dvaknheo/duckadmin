<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckAdmin\User\Controller;

use DuckPhp\Foundation\SessionTrait;
use DuckPhp\Foundation\Controller\UserSessionTrait;

/**
 * Session 本质上和 Model 一样不做逻辑运算，不抛异常
 */
class Session 
{
    use SessionTrait;
    use UserSessionTrait;

    ////////////////////////////////////////////////////////////////////////
    public function csrfToken()
    {
        $token = $this->get('_token');
        if (!isset($token)) {
            $token = $this->randomString(40);
            $this->set('_token', $token);
        }
        return $token;
    }
    public function csrfField()
    {
        return '<input type="hidden" name="_token" value="'.$this->csrfToken().'">';
    }
    ////////////////////////////////////////////////
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
