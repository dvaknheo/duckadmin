<?php

namespace plugin\admin\app\controller;

use plugin\admin\app\common\Auth;
use plugin\admin\app\model\Admin;
use plugin\admin\app\model\AdminRole;
use support\exception\BusinessException;
use support\Request;
use support\Response;

/**
 * 管理员列表 
 */
class AdminController extends Crud
{
    /**
     * 不需要鉴权的方法
     * @var array
     */
    protected $noNeedAuth = ['select'];

    /**
     * @var Admin
     */
    protected $model = null;

    /**
     * 开启auth数据限制
     * @var string
     */
    protected $dataLimit = 'auth';

    /**
     * 以id为数据限制字段
     * @var string
     */
    protected $dataLimitField = 'id';

    /**
     * 浏览
     * @return Response
     */
    public function index()
    {
        C::ThrowOn(true,"No Impelement");
    }

    /**
     * 查询
     * @param 
     * @return Response
     * @throws BusinessException
     */
    public function select()
    {
        C::ThrowOn(true,"No Impelement");
    }

    /**
     * 插入
     * @param 
     * @return Response
     * @throws BusinessException
     */
    public function insert()
    {
        C::ThrowOn(true,"No Impelement");
    }

    /**
     * 更新
     * @param 
     * @return Response
     * @throws BusinessException
    */
    public function update()
    {
        C::ThrowOn(true,"No Impelement");

    }

    /**
     * 删除
     * @param 
     * @return Response
     */
    public function delete()
    {
        C::ThrowOn(true,"No Impelement");
    }

}
