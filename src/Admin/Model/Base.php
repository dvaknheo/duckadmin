<?php declare(strict_types=1);
/**
 * DuckPhp Admin System - Model Base
 */
namespace DuckAdmin\Admin\Model;

use DuckPhp\Component\DbManager;
use DuckPhp\Foundation\Model\Base as ModelBase;

class Base extends ModelBase
{   
    /**
     * 获取最后插入的 ID
     */
    public function lastInsertId(): ?string
    {
        return DbManager::_()->_DbForWrite()->lastInsertId();
    }
}
