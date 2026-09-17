<?php declare(strict_types=1);
namespace DuckAdmin\Admin\Controller;

use DuckAdmin\Admin\Business\AdminBusiness;

/** 
 * @menu_directory 下属人员
 * @menu_weight 97
 */
class AdminController extends Base
{
    /**
     * @menu 查看下属
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

        $currentUserId = (int)Session::_()->getCurrentAdminId();
        $data = AdminBusiness::_()->getList($currentUserId, $page, $pageSize, $search);
        $data['page'] = $page;
        $data['pageSize'] = $pageSize;
        $data['search'] = $search;
        $data['title'] = '人员管理';

        $data['urls'] = [
            'list' => __url('Admin/index'),
            'create' => __url('Admin/create'),
            'edit' => __url('Admin/edit'),
            'delete' => __url('Admin/delete'),
        ];

        Helper::Show($data, 'AdminNew/user_list');
    }

    /**
     * @menu_action 添加下属
     */
    public function create()
    {
        $data['title'] = '新增人员';
        $data['urls'] = [
            'save' => __url('Admin/save'),
            'list' => __url('Admin/index'),
        ];
        $data['is_edit'] = false;
        Helper::Show($data, 'AdminNew/user_form');
    }

    /**
     * @menu_action 保存下属
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

        try {
            AdminBusiness::_()->create($input);
            Helper::Show302(__url('Admin/index'));
            return;
        } catch (\Exception $ex) {
            $data['error'] = $ex->getMessage();
        }
        $data['title'] = '新增人员';
        $data['input'] = $input;
        $data['urls'] = [
            'save' => __url('Admin/save'),
            'list' => __url('Admin/index'),
        ];
        $data['is_edit'] = false;
        Helper::Show($data, 'AdminNew/user_form');
    }

    /**
     * @menu_action 编辑下属
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
        $data['urls'] = [
            'update' => __url('Admin/update'),
            'list' => __url('Admin/index'),
        ];
        $data['is_edit'] = true;
        Helper::Show($data, 'AdminNew/user_form');
    }

    /**
     * @menu_action 更新下属
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

        try {
            AdminBusiness::_()->update($id, $input);
            Helper::Show302(__url('Admin/index'));
            return;
        } catch (\Exception $ex) {
            $data['error'] = $ex->getMessage();
        }
        $data['user'] = $input + ['id' => $id];
        $data['title'] = '编辑人员';
        $data['urls'] = [
            'update' => __url('Admin/update'),
            'list' => __url('Admin/index'),
        ];
        $data['is_edit'] = true;
        Helper::Show($data, 'AdminNew/user_form');
    }

    /**
     * @menu_action 删除下属
     */
    public function delete()
    {
        $id = (int)Helper::GET('id', '0');
        AdminBusiness::_()->delete($id);
        Helper::Show302(__url('Admin/index'));
    }
}
