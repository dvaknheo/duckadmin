<?php declare(strict_types=1);
namespace DuckAdmin\Admin\Controller;

use DuckAdmin\Admin\Business\AdminBusiness;

/**
 * @menu_group 系统管理 20
 * @menu_directory 人员管理 Admin/index
 * @menu_weight 10
 */
class AdminController extends Base
{
    /**
     * @menu_item 人员管理
     * @menu_weight 10
     */
    public function index()
    {
        $page = max(1, (int)(Helper::GET('page', '1')));
        $pageSize = 15;

        $search = [
            'username' => Helper::GET('username', ''),
            'realname' => Helper::GET('realname', ''),
            'email' => Helper::GET('email', ''),
        ];

        $data = AdminBusiness::_()->getList($page, $pageSize, $search);
        $data['page'] = $page;
        $data['pageSize'] = $pageSize;
        $data['search'] = $search;
        $data['title'] = '人员管理';
        $data['current_route'] = 'admin';

        $data['urls'] = [
            'list' => __url('Admin/index'),
            'create' => __url('Admin/create'),
            'edit' => __url('Admin/edit'),
            'delete' => __url('Admin/delete'),
        ];

        Helper::Show($data, 'AdminNew/user_list');
    }

    /**
     * @menu_action 新增人员
     * @menu_weight 20
     */
    public function create()
    {
        $data['title'] = '新增人员';
        $data['current_route'] = 'admin';
        $data['urls'] = [
            'save' => __url('Admin/save'),
            'list' => __url('Admin/index'),
        ];
        $data['is_edit'] = false;
        Helper::Show($data, 'AdminNew/user_form');
    }

    /**
     * @menu_action 保存人员
     * @menu_weight 21
     */
    public function save()
    {
        $input = [
            'username' => Helper::POST('username', ''),
            'password' => Helper::POST('password', ''),
            'realname' => Helper::POST('realname', ''),
            'email' => Helper::POST('email', ''),
            'status' => (int)Helper::POST('status', '1'),
        ];

        $result = AdminBusiness::_()->create($input);
        if ($result['success']) {
            Helper::Show302(__url('Admin/index'));
            return;
        }
        $data['error'] = $result['message'];
        $data['title'] = '新增人员';
        $data['current_route'] = 'admin';
        $data['input'] = $input;
        $data['urls'] = [
            'save' => __url('Admin/save'),
            'list' => __url('Admin/index'),
        ];
        $data['is_edit'] = false;
        Helper::Show($data, 'AdminNew/user_form');
    }

    /**
     * @menu_action 编辑人员
     * @menu_weight 22
     */
    public function edit()
    {
        $id = (int)Helper::GET('id', '0');
        $user = AdminBusiness::_()->getById($id);
        if (!$user) {
            Helper::Show302(__url('Admin/index'));
            return;
        }

        $data['user'] = $user;
        $data['title'] = '编辑人员';
        $data['current_route'] = 'admin';
        $data['urls'] = [
            'update' => __url('Admin/update'),
            'list' => __url('Admin/index'),
        ];
        $data['is_edit'] = true;
        Helper::Show($data, 'AdminNew/user_form');
    }

    /**
     * @menu_action 更新人员
     * @menu_weight 23
     */
    public function update()
    {
        $id = (int)Helper::POST('id', '0');
        $input = [
            'username' => Helper::POST('username', ''),
            'password' => Helper::POST('password', ''),
            'realname' => Helper::POST('realname', ''),
            'email' => Helper::POST('email', ''),
            'status' => (int)Helper::POST('status', '1'),
        ];

        $result = AdminBusiness::_()->update($id, $input);
        if ($result['success']) {
            Helper::Show302(__url('Admin/index'));
            return;
        }
        $data['error'] = $result['message'];
        $data['user'] = $input + ['id' => $id];
        $data['title'] = '编辑人员';
        $data['current_route'] = 'admin';
        $data['urls'] = [
            'update' => __url('Admin/update'),
            'list' => __url('Admin/index'),
        ];
        $data['is_edit'] = true;
        Helper::Show($data, 'AdminNew/user_form');
    }

    /**
     * @menu_action 删除人员
     * @menu_weight 24
     */
    public function delete()
    {
        $id = (int)Helper::GET('id', '0');
        AdminBusiness::_()->delete($id);
        Helper::Show302(__url('Admin/index'));
    }
}
