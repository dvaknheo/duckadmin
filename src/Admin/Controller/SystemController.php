<?php declare(strict_types=1);
namespace DuckAdmin\Admin\Controller;

use DuckAdmin\Admin\Business\PermissionBusiness;

/**
 * @menu 权限和菜单管理
 */
class SystemController extends Base
{
    /**
     * @name 权限和菜单管理
     * 菜单/权限列表
     */
    public function index()
    {
        $page = max(1, (int)(Helper::GET('page', '1')));
        $search = Helper::GET('search', '');
        $pageSize = 15;

        $data = PermissionBusiness::_()->getList($page, $pageSize, $search);
        $data['page'] = $page;
        $data['pageSize'] = $pageSize;
        $data['search'] = $search;
        $data['title'] = '权限和菜单管理';
        $data['current_route'] = 'system';

        $this->render('admin/system_index', $data);
    }

    /**
     * @name 一键扫描
     * 扫描路由,缺失的权限/菜单自动入库
     */
    public function scan()
    {
        $added = PermissionBusiness::_()->scanRoutes();
        $data['added'] = $added;
        $data['title'] = '一键扫描结果';
        $data['current_route'] = 'system';
        $this->render('admin/system_scan', $data);
    }

    /**
     * @name 新增菜单
     */
    public function create()
    {
        $data['title'] = '新增菜单';
        $data['current_route'] = 'system';
        $data['permissions'] = PermissionBusiness::_()->getAll();
        $this->render('admin/system_form', $data);
    }

    /**
     * @name 保存菜单
     */
    public function save()
    {
        $input = [
            'name' => Helper::POST('name', ''),
            'url' => Helper::POST('url', ''),
            'type' => (int)Helper::POST('type', '1'),
            'parent_id' => (int)Helper::POST('parent_id', '0'),
            'weight' => (int)Helper::POST('weight', '0'),
        ];

        $result = PermissionBusiness::_()->create($input);
        if ($result['success']) {
            Helper::Show302(__url('system/index'));
        } else {
            $data['error'] = $result['message'];
            $data['title'] = '新增菜单';
            $data['current_route'] = 'system';
            $data['input'] = $input;
            $data['permissions'] = PermissionBusiness::_()->getAll();
            $this->render('admin/system_form', $data);
        }
    }

    /**
     * @name 编辑菜单
     */
    public function edit()
    {
        $id = (int)Helper::GET('id', '0');
        $perm = PermissionBusiness::_()->getById($id);
        if (!$perm) {
            Helper::Show302(__url('system/index'));
            return;
        }

        $data['perm'] = $perm;
        $data['title'] = '编辑菜单';
        $data['current_route'] = 'system';
        $data['permissions'] = PermissionBusiness::_()->getAll();
        $this->render('admin/system_form', $data);
    }

    /**
     * @name 更新菜单
     */
    public function update()
    {
        $id = (int)Helper::POST('id', '0');
        $input = [
            'name' => Helper::POST('name', ''),
            'url' => Helper::POST('url', ''),
            'type' => (int)Helper::POST('type', '1'),
            'parent_id' => (int)Helper::POST('parent_id', '0'),
            'weight' => (int)Helper::POST('weight', '0'),
        ];

        $result = PermissionBusiness::_()->update($id, $input);
        if ($result['success']) {
            Helper::Show302(__url('system/index'));
        } else {
            $data['error'] = $result['message'];
            $data['perm'] = $input + ['id' => $id];
            $data['title'] = '编辑菜单';
            $data['current_route'] = 'system';
            $data['permissions'] = PermissionBusiness::_()->getAll();
            $this->render('admin/system_form', $data);
        }
    }

    /**
     * @name 删除菜单
     */
    public function delete()
    {
        $id = (int)Helper::GET('id', '0');
        PermissionBusiness::_()->delete($id);
        Helper::Show302(__url('system/index'));
    }
}
