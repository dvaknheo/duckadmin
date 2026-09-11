<?php declare(strict_types=1);
namespace DuckAdmin\Admin\Controller\System;

use DuckAdmin\Admin\Business\UserBusiness;
use DuckAdmin\Admin\Business\RoleBusiness;

/**
 * @menu_group 人员管理
 */
class AdminController extends Base
{
    /** @menu 人员管理 */
    public function index()
    {
        $page = max(1, (int)(Helper::GET('page', '1')));
        $search = Helper::GET('search', '');
        $pageSize = 15;
        
        $data = UserBusiness::_()->getList($page, $pageSize, $search);
        $data['page'] = $page;
        $data['pageSize'] = $pageSize;
        $data['search'] = $search;
        $data['title'] = '人员管理';
        $data['current_route'] = 'user';
        
        Helper::Show($data, 'admin/user_list');
    }
    

    /** @action 新增人员 */
    public function create()
    {
        $admin_id = (int)Session::_()->getUserId();
        $data['title'] = '新增人员';
        $data['current_route'] = 'user';
        $data['roles'] = RoleBusiness::_()->getAllManageable($admin_id);
        Helper::Show($data, 'admin/user_form');
    }
    

    /** @action 保存人员 */
    public function save()
    {
        $input = [
            'username' => Helper::POST('username', ''),
            'password' => Helper::POST('password', ''),
            'realname' => Helper::POST('realname', ''),
            'email' => Helper::POST('email', ''),
            'status' => (int)Helper::POST('status', '1'),
        ];
        $roleIds = Helper::POST('role_ids', []);
        $roleIds = is_array($roleIds) ? $roleIds : [];
        
        $result = UserBusiness::_()->create($input, $roleIds);
        if ($result['success']) {
            Helper::Show302(__url('user/index'));
            return;
        }
        $admin_id = (int)Session::_()->getUserId();
        $data['error'] = $result['message'];
        $data['title'] = '新增人员';
        $data['current_route'] = 'user';
        $data['roles'] = RoleBusiness::_()->getAllManageable($admin_id);
        $data['input'] = $input;
        Helper::Show($data, 'admin/user_form');
    }
    
    /** @action 编辑人员 */
    public function edit()
    {
        $id = (int)Helper::GET('id', '0');
        $user = UserBusiness::_()->getById($id);
        if (!$user) {
            Helper::Show302(__url('user/index'));
            return;
        }
        
        $data['user'] = $user;
        $data['title'] = '编辑人员';
        $data['current_route'] = 'user';
        $admin_id = (int)Session::_()->getUserId();
        $data['roles'] = RoleBusiness::_()->getAllManageable($admin_id);
        $data['user_role_ids'] = RoleBusiness::_()->getUserRoleIds($id);
        Helper::Show($data, 'admin/user_form');
    }
    
    /** @action 更新人员 */
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
        $roleIds = Helper::POST('role_ids', []);
        $roleIds = is_array($roleIds) ? $roleIds : [];
        
        $result = UserBusiness::_()->update($id, $input, $roleIds);
        if ($result['success']) {
            Helper::Show302(__url('user/index'));
            return;
        }
        $admin_id = (int)Session::_()->getUserId();
        $data['error'] = $result['message'];
        $data['user'] = $input + ['id' => $id];
        $data['title'] = '编辑人员';
        $data['current_route'] = 'user';
        $data['roles'] = RoleBusiness::_()->getAllManageable($admin_id);
        $data['user_role_ids'] = $roleIds;
        Helper::Show($data, 'admin/user_form');
        
    }
    
    /** @action 删除人员 */
    public function delete()
    {
        $id = (int)Helper::GET('id', '0');
        UserBusiness::_()->delete($id);
        Helper::Show302(__url('user/index'));
    }
}


