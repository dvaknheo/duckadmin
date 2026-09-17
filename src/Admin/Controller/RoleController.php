<?php declare(strict_types=1);
namespace DuckAdmin\Admin\Controller;

use DuckAdmin\Admin\Business\RoleBusiness;

/**
 * @menu_directory 下属职位
 * @menu_weight 98
 */
class RoleController extends Base
{
    /**
     * @menu 查看职位
     * @menu_weight 10
     */
    public function index()
    {
        $page = max(1, (int)(Helper::GET('page', '1')));
        $search = Helper::GET('search', '');
        $pageSize = 15;

        $data = RoleBusiness::_()->getList($page, $pageSize, $search);
        $data['page'] = $page;
        $data['pageSize'] = $pageSize;
        $data['search'] = $search;
        $data['title'] = '职位管理';

        $data['urls'] = [
            'list' => __url('Role/index'),
            'create' => __url('Role/create'),
            'edit' => __url('Role/edit'),
            'delete' => __url('Role/delete'),
            'permissions' => __url('Permission/index'),
        ];

        Helper::Show($data, 'Role/role_list');
    }

    /**
     * @menu_action 新增职位
     * @menu_weight 20
     */
    public function create()
    {
        $data['title'] = '新增职位';
        $data['roles'] = RoleBusiness::_()->getAll();
        $data['urls'] = [
            'save' => __url('Role/save'),
            'list' => __url('Role/index'),
        ];
        $data['is_edit'] = false;
        Helper::Show($data, 'Role/role_form');
    }

    /**
     * @menu_action 保存职位
     * @menu_weight 21
     */
    public function save()
    {
        $name = Helper::POST('name', '');
        $description = Helper::POST('description', '');
        $pid = (int)Helper::POST('pid', '0');

        try {
            RoleBusiness::_()->create($name, $description, $pid);
            Helper::Show302(__url('Role/index'));
        } catch (\Exception $ex) {
            $data['error'] = $ex->getMessage();
            $data['title'] = '新增职位';
            $data['input'] = ['name' => $name, 'description' => $description, 'pid' => $pid];
            $data['roles'] = RoleBusiness::_()->getAll();
            $data['urls'] = [
                'save' => __url('Role/save'),
                'list' => __url('Role/index'),
            ];
            $data['is_edit'] = false;
            Helper::Show($data, 'Role/role_form');
        }
    }

    /**
     * @menu_action 编辑职位
     * @menu_weight 22
     */
    public function edit()
    {
        $id = (int)Helper::GET('id', '0');
        $role = RoleBusiness::_()->getById($id);
        if (!$role) {
            Helper::Show302(__url('Role/index'));
            return;
        }

        $data['role'] = $role;
        $data['title'] = '编辑职位';
        $data['roles'] = RoleBusiness::_()->getAll();
        $data['urls'] = [
            'update' => __url('Role/update'),
            'list' => __url('Role/index'),
        ];
        $data['is_edit'] = true;
        Helper::Show($data, 'Role/role_form');
    }

    /**
     * @menu_action 更新职位
     * @menu_weight 23
     */
    public function update()
    {
        $id = (int)Helper::POST('id', '0');
        $name = Helper::POST('name', '');
        $description = Helper::POST('description', '');
        $pid = (int)Helper::POST('pid', '0');

        try {
            RoleBusiness::_()->update($id, $name, $description, $pid);
            Helper::Show302(__url('Role/index'));
        } catch (\Exception $ex) {
            $data['error'] = $ex->getMessage();
            $data['role'] = ['id' => $id, 'name' => $name, 'description' => $description, 'pid' => $pid];
            $data['title'] = '编辑职位';
            $data['roles'] = RoleBusiness::_()->getAll();
            $data['urls'] = [
                'update' => __url('Role/update'),
                'list' => __url('Role/index'),
            ];
            $data['is_edit'] = true;
            Helper::Show($data, 'Role/role_form');
        }
    }

    /**
     * @menu_action 删除职位
     * @menu_weight 24
     */
    public function delete()
    {
        $id = (int)Helper::GET('id', '0');
        RoleBusiness::_()->delete($id);
        Helper::Show302(__url('Role/index'));
    }
}
