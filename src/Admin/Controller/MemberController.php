<?php declare(strict_types=1);
namespace DuckAdmin\Admin\Controller;

use DuckAdmin\Admin\Business\MemberBusiness;
use DuckAdmin\Admin\Business\MemberRoleBusiness;

/**
 * @menu_group 成员管理 10
 * @menu_directory 下属人员 Member/index
 * @menu_weight 10
 */
class MemberController extends Base
{

    /**
     * @menu_item 下属人员
     * @menu_weight 10
     */
    public function index()
    {
        $page = max(1, (int)(Helper::GET('page', '1')));
        $search = Helper::GET('search', '');
        $pageSize = 15;
        
        $data = MemberBusiness::_()->getList($page, $pageSize, $search);
        $data['page'] = $page;
        $data['pageSize'] = $pageSize;
        $data['search'] = $search;
        $data['title'] = '人员管理';
        $data['current_route'] = 'user';
        
        Helper::Show($data, 'admin/user_list');
    }
    

    /**
     * @menu_action 新增人员
     * @menu_weight 20
     */
    public function create()
    {
        $admin_id = (int)Session::_()->getUserId();
        $data['title'] = '新增人员';
        $data['current_route'] = 'user';
        $data['roles'] = MemberRoleBusiness::_()->getAllManageable($admin_id);
        Helper::Show($data, 'admin/user_form');
    }
    
    /**
     * 保存新用户
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
        $roleIds = Helper::POST('role_ids', []);
        $roleIds = is_array($roleIds) ? $roleIds : [];
        
        $result = MemberBusiness::_()->create($input, $roleIds);
        if ($result['success']) {
            Helper::Show302(__url('Member/index'));
            return;
        }
        $admin_id = (int)Session::_()->getUserId();
        $data['error'] = $result['message'];
        $data['title'] = '新增人员';
        $data['current_route'] = 'user';
        $data['roles'] = MemberRoleBusiness::_()->getAllManageable($admin_id);
        $data['input'] = $input;
        Helper::Show($data, 'admin/user_form');
    }
    
    /**
     * 编辑用户表单
     * @menu_action 编辑人员
     * @menu_weight 22
     */
    public function edit()
    {
        $id = (int)Helper::GET('id', '0');
        $user = MemberBusiness::_()->getById($id);
        if (!$user) {
            Helper::Show302(__url('Member/index'));
            return;
        }
        
        $data['user'] = $user;
        $data['title'] = '编辑人员';
        $data['current_route'] = 'user';
        $admin_id = (int)Session::_()->getUserId();
        $data['roles'] = MemberRoleBusiness::_()->getAllManageable($admin_id);
        $data['user_role_ids'] = MemberRoleBusiness::_()->getUserRoleIds($id);
        Helper::Show($data, 'admin/user_form');
    }
    
    /**
     * 更新用户
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
        $roleIds = Helper::POST('role_ids', []);
        $roleIds = is_array($roleIds) ? $roleIds : [];
        
        $result = MemberBusiness::_()->update($id, $input, $roleIds);
        if ($result['success']) {
            Helper::Show302(__url('Member/index'));
            return;
        }
        $admin_id = (int)Session::_()->getUserId();
        $data['error'] = $result['message'];
        $data['user'] = $input + ['id' => $id];
        $data['title'] = '编辑人员';
        $data['current_route'] = 'user';
        $data['roles'] = MemberRoleBusiness::_()->getAllManageable($admin_id);
        $data['user_role_ids'] = $roleIds;
        Helper::Show($data, 'admin/user_form');
        
    }
    
    /**
     * 删除用户
     * @menu_action 删除人员
     * @menu_weight 24
     */
    public function delete()
    {
        $id = (int)Helper::GET('id', '0');
        MemberBusiness::_()->delete($id);
        Helper::Show302(__url('Member/index'));
    }
}


