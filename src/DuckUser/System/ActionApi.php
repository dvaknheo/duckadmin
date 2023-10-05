<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckUser\System;

use DuckPhp\Foundation\SimpleApiTrait;
use DuckPhp\Controller\UserAction;

class ActionApi
{
    use SimpleApiTrait;
    //
    public function id()
    {
        return UserAction::G()->id();
    }
    public function data()
    {
        return UserAction::G()->data();
    }
}