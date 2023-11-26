<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckUser\System;

use DuckPhp\Foundation\SimpleActionTrait;
use DuckPhp\Controller\UserAction;

class UserApi
{
    use SimpleActionTrait;
    //
    public function id()
    {
        return UserAction::_()->id();
    }
    public function data()
    {
        return UserAction::_()->data();
    }
}