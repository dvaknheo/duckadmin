<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Api;

use DuckAdmin\System\BaseController;
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
        return BaseController::CheckPermission();
    }
    protected function CheckCapthca($captcha)
    {
        return BaseController::CheckCapthca($captcha);
    }
    public function CaptchaBuilder()
    {
        return BaseController::CaptchaBuilder();
    }
}