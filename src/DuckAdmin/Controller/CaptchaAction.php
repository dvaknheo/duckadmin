<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\ControllerEx;

use DuckAdmin\Controller\AdminSession;
use DuckAdmin\System\ProjectAction;

use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;

/**
 * 验证码系统，你可以修改你的实现
 * 第三方的东西在这里写
 */
class CaptchaAction extends ProjectAction
{
    public function __construct()
    {
    }
    //////// 验证码部分 ////////
    public static function ShowCaptcha()
    {
        return static::G()->doShowCaptcha();
    }
    public static function CheckCaptcha($captcha)
    {
        return static::G()->doCheckCaptcha($captcha);
    }
    public function doShowCaptcha()
    {
        $phraseBuilder = new PhraseBuilder(4, '0123456789');
        $builder = new CaptchaBuilder(null, $phraseBuilder);
        $builder->build();
        
        $phrase = $builder->getPhrase();
        AdminSession::G()->setPhrase($phrase);  // 这个 想处理掉，难受。
        
        static::header('Content-type: image/jpeg');
        static::header('Cache-Control: no-cache, no-store, max-age=0, must-revalidate');
        $builder->output();
    }

    public function doCheckCaptcha($captcha)
    {
        $phrase = AdminSession::G()->getPhrase(); // 这个 关联也想处理掉。
        
        $flag = PhraseBuilder::comparePhrases($phrase, $captcha);
        return $flag;
    }
}