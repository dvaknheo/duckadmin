<?php declare(strict_types=1);
/**
 * DuckPhp Admin System - Controller Base
 * 所有后台认证页面的基类
 */
namespace DuckAdmin\Admin\Controller;

use DuckPhp\Foundation\Controller\AdminControllerBase;

class Base extends AdminControllerBase
{
    public function initController()
    {
        parent::initController();
        Helper::assignViewData('__logined_enable_view', true);
        Helper::assignViewData('__logined_enable_header_footer', true);
    }
}