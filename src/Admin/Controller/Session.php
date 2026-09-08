<?php declare(strict_types=1);
/**
 * DuckPhp Admin System - Session 管理
 */
namespace DuckAdmin\Admin\Controller;

use DuckPhp\Foundation\SessionTrait;
use DuckPhp\Foundation\Controller\AdminSessionTrait;

class Session
{
    use SessionTrait;
    use AdminSessionTrait;
    
    /**
     * 获取当前用户姓名
     */
    public function getRealname(): ?string
    {
        return $this->get('realname');
    }
}
