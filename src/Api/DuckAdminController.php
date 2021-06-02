<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Api;

use DuckAdmin\System\Controller;
/**
 * 你的 Contoller 控制器调用这里的静态方法类。
 */
class DuckAdminController
{
    public function __construct()
    {
    }
    public static function CheckPermission()
    {
        return Controller::CheckPermission();
    }
    protected function CheckCapthca($captcha)
    {
        return Controller::CheckCapthca($captcha);
    }
    public function CaptchaBuilder()
    {
        return Controller::CaptchaBuilder();
    }
}