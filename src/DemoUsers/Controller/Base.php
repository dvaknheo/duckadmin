<?php declare(strict_types=1);
/**
 * DuckAdmin DemoUsers - Controller Base
 */
namespace DuckAdmin\DemoUsers\Controller;

class Base
{
    public function __construct()
    {
        Helper::setViewHeadFoot('_sys/inc-head', '_sys/inc-foot');
    }
}
