<?php declare(strict_types=1);
/**
 * DuckAdmin SingleAdmin - Controller Base
 */
namespace DuckAdmin\SingleAdmin\Controller;

class Base
{
    public function __construct()
    {
        Helper::setViewHeadFoot('_sys/inc-head', '_sys/inc-foot');
    }
}
