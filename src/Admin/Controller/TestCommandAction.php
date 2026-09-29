<?php declare(strict_types=1);

namespace DuckAdmin\Admin\Controller;

use DuckAdmin\Admin\Business\TestBusiness;
use DuckPhp\Foundation\SingletonTrait;
//@codeCoverageIgnoreStart
class TestCommandAction
{
    use SingletonTrait;

    /**
     * 重新在数据库里生成权限菜单
     * @return void
     */
    public function command_rebuild_menu()
    {
        $ret = TestBusiness::_()->rebuild_menu();

        echo "\n".DATE(DATE_ATOM)."\n";
    }
    /**
     * test something
     */
    public function command_cache_menu()
    {
        $ret = TestBusiness::_()->cache_menu();
        echo json_encode($ret,JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        echo "\n".DATE(DATE_ATOM)."\n";
    }
} //@codeCoverageIgnoreEnd