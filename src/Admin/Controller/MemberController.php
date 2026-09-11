<?php declare(strict_types=1);
namespace DuckAdmin\Admin\Controller;

use DuckAdmin\Admin\Business\MemberBusiness;
use DuckAdmin\Admin\Business\MemberRoleBusiness;

/**
 * @menu_group 下属人员管理
 */
class MemberController extends Base
{

    /** @menu 下属列表 */
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
    

    /** @action 新增人员 */
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
     */
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
        
        $result = MemberBusiness::_()->create($input, $roleIds);
        if ($result['success']) {
            Helper::Show302(__url('user/index'));
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
     */
    /** @action 编辑人员 */
    public function edit()
    {
        $id = (int)Helper::GET('id', '0');
        $user = MemberBusiness::_()->getById($id);
        if (!$user) {
            Helper::Show302(__url('user/index'));
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
     */
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
        
        $result = MemberBusiness::_()->update($id, $input, $roleIds);
        if ($result['success']) {
            Helper::Show302(__url('user/index'));
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
     */
    /** @action 删除人员 */
    public function delete()
    {
        $id = (int)Helper::GET('id', '0');
        MemberBusiness::_()->delete($id);
        Helper::Show302(__url('user/index'));
    }
}


