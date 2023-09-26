<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckUser\Api;

use DuckPhp\SingletonEx\SingletonExTrait;

class ActionApi
{
    use SingletonExTrait;
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