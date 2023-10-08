<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Controller;

use DuckPhp\Core\App;
use DuckPhp\Core\ComponentBase;

use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;

/**
 * 验证码系统，你可以修改你的实现
 * 第三方的东西在这里写
 */
class CaptchaAction extends ComponentBase
{
	public $options = [
        'set_phrase_handler' => null,
        'get_phrase_handler' => null,
    ];
    public function __construct()
    {
    }
    //////// 验证码部分 ////////
    public static function ShowCaptcha()
    {
        return static::_()->doShowCaptcha();
    }
    public static function CheckCaptcha($captcha)
    {
        return static::_()->doCheckCaptcha($captcha);
    }
    public function doShowCaptcha()
    {
        $phraseBuilder = new PhraseBuilder(4, '0123456789');
        $builder = new CaptchaBuilder(null, $phraseBuilder);
        $builder->build();
        
        $phrase = $builder->getPhrase();
        
        ($this->options['set_phrase_handler'])($phrase);
        
        App::header('Content-type: image/jpeg');
        App::header('Cache-Control: no-cache, no-store, max-age=0, must-revalidate');
        $builder->output();
    }

    public function doCheckCaptcha($captcha)
    {
        $phrase = ($this->options['get_phrase_handler'])();
        
        $flag = PhraseBuilder::comparePhrases($phrase, $captcha);
        return $flag;
    }
}