<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckAdmin\User\Controller;

use DuckPhp\Foundation\Controller\SessionTrait;
use DuckPhp\GlobalUser\UserSessionInterface;
use DuckPhp\GlobalUser\UserSessionTrait;

/**
 * Session 本质上和 Model 一样不做逻辑运算，不抛异常
 */
class Session implements UserSessionInterface
{
    use SessionTrait;
    use UserSessionTrait;
    //@override

    ////////////////////////////////////////////////////////////////////////
    // getHash()/checkHash() 由 AdminController 调用（跨类调用），必须是 public。
    // 原来是 protected + 控制器里的同名副本（副本带 `return;` 早退），
    // 副本删掉、方法移到这里之后就暴露成跨类调用 protected 的致命错误了。
    public function getHash($id)
    {
        $hash_id = $this->get('hash',null);
        $hash_id = $hash_id??mt_rand(1,999999);
        $_SESSION['hash']=$hash_id;
        return md5($hash_id.'|'.$id);
    }
    public function checkHash($id, $hash)
    {
        $hash_id = $this->get('hash',null);
        Helper::ControllerThrowOn($hash_id===null,"校[$id, $hash]检失败!");
        $new_hash = md5($hash_id.'|'.$id);
        Helper::ControllerThrowOn($new_hash!==$hash,"校 [$id, $hash, $new_hash] 检失败");
    }

    /**
     * 注意:读写必须用同一个 key,否则每次调用都会重新生成(原来读 'token' 写 '_token')
     */
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
