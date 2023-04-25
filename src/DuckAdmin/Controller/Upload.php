<?php

namespace plugin\admin\app\controller;

use Exception;
use Intervention\Image\ImageManagerStatic as Image;
use plugin\admin\app\controller\Base;
use plugin\admin\app\controller\Crud;
use plugin\admin\app\model\Upload;
use support\exception\BusinessException;
use support\Request;
use support\Response;

/**
 * 附件管理 
 */
class UploadController extends Crud
{
    /**
     * @var Upload
     */
    protected $model = null;

    /**
     * 只返回当前管理员数据
     * @var string
     */
    protected $dataLimit = 'personal';

    /**
     * 构造函数
     * @return void
     */

    
    /**
     * 浏览
     * @return Response
     */
    public function index(): Response
    {
        return view('upload/index');
    }

    /**
     * 浏览附件
     * @return Response
     */
    public function attachment(): Response
    {
        return view('upload/attachment');
    }

    /**
     * 查询附件
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function select(Request $request): Response
    {
    }

    /**
     * 更新附件
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function update(Request $request): Response
    {
        if ($request->method() === 'GET') {
            return view('upload/update');
        }
        return parent::update($request);
    }

    /**
     * 添加附件
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function insert(Request $request): Response
    {
        
    }

    /**
     * 上传文件
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function file(Request $request): Response
    {
   
    }

    /**
     * 上传图片
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function image(Request $request): Response
    {
     
    }

    /**
     * 上传头像
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function avatar(Request $request): Response
    {
    }

    /**
     * 删除附件
     * @param Request $request
     * @return Response
     */
    public function delete(Request $request): Response
    {
        return parent::delete($request);
    }


}
