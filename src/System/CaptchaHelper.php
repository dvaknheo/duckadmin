<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\System;

use DuckPhp\SingletonEx\SingletonExTrait;
use DuckAdmin\System\App as C;
use DuckAdmin\Business\SessionBusiness;

use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;

/**
 * 这是充当 Helper 助手的 控制器基类
 * 这里要注意的是，控制器的公开动态方法都会当成 web 动作，所以尽量避免公开动态方法
 * 第三方的东西在这里写
 */
class CaptchaHelper
{
    use SingletonExTrait;
    
    //////// 验证码部分 ////////
    public function doShowCaptcha()
    {
        $phraseBuilder = new PhraseBuilder(4, '0123456789');
        $builder = new CaptchaBuilder(null, $phraseBuilder);
        $builder->build();
        
        $phrase = $builder->getPhrase();
        SessionBusiness::G()->setPhrase($phrase);  // 这个 想处理掉，难受。
        
        C::header('Content-type: image/jpeg');
        C::header('Cache-Control: no-cache, no-store, max-age=0, must-revalidate');
        $builder->output();
    }
    public function doCheckCaptcha($captcha)
    {
        $builder = new CaptchaBuilder();
        $phrase = SessionBusiness::G()->getPhrase(); // 这个 关联也想处理掉。
        $flag = PhraseBuilder::comparePhrases($phrase, $captcha);
        return $flag;
    }
}