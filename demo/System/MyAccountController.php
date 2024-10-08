<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */
namespace DuckAdminDemo\System;


use DuckAdmin\Controller\AccountController;

class MyAccountController extends AccountController
{
    public function dashboard()
    {
        echo "<h1>这里已经被 ".static::class." 重载</h1>";
        parent::dashboard();
    }
}