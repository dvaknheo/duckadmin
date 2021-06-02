<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Controller;

class Role extends Base
{
    /**
     * 角色
     */
    public function index()
    {
        C::Show(get_defined_vars(),'role/index');
    }

    /**
     * 添加
     */
    public function add()
    {
        C::Show(get_defined_vars());
    }

     /**
     * 编辑
     */
    public function edit($id)
    { 
    }

    /**
     * 删除
     */
    public function remove($id)
    {
    }
}
