<?php declare(strict_types=1);
/**
 * DuckPhp Admin System - Setting Business
 * 系统设置业务（超管用）
 */
namespace DuckAdmin\Admin\Business;

use DuckAdmin\Admin\Model\PermissionModel;
use DuckPhp\Component\DbManager;

class TestBusiness extends Base
{
    public function test()
    {
        return AdminTreeBuilder::_()->build();
    }
}
