<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckPear\Controller;

class Permission extends Base
{
    /**
     * 权限
     */
    public function index()
    {
        var_dump("权限列表");
    }

    /**
     * 添加
     */
    public function add()
    {
    }

     /**
     * 编辑
     */
    public function edit($id)
    { 
    }

    /**
     * 禁用，启用
     */
    public function status($id)
    {
    }
    /**
     * 删除
     */
    public function remove($id,$type=false)
    {
    }
}

