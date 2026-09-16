<?php declare(strict_types=1);
/**
 * DuckPhp
 *
 * To enable this command class, uncomment the following application option:
 *   'cmd' => [CommandAction::class => true]
 *
 * Provides a sample CLI command. Run `php ./cli.php hello` to execute
 * CommandAction::_()->command_hello().
 */
namespace DuckAdmin\Admin\Controller;

use DuckPhp\Foundation\SingletonTrait;
use DuckAdmin\Admin\Business\TestBusiness;
class CommandAction
{
    use SingletonTrait;

    /**
     * test something
     */
    public function command_test()
    {
        $tree = TestBusiness::_()->testInstall();
        echo json_encode($tree,JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

}
