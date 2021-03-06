<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckUser\System;


use DuckPhp\Foundation\Session;
use DuckPhp\Foundation\ThrowOnableTrait;

class ProjectSession extends Session
{
    use ThrowOnableTrait;
    public function __construct()
    {
        parent::__construct();
        $this->options['session_prefix'] = App::G()->options['session_prefix'];
        $this->exception_class = ProjectException::class;
    }
}
