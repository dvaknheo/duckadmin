<?php
namespace DuckAdmin\Service;

use DuckAdmin\Model\AdminModel;

class AdminService extends BaseService
{
    public function login(array $data)
    {
        $admin = AdminModel::G()->login($data['username'], $data['password']);
        ServiceException::ThrowOn(empty($admin), "用户名或密码不正确，无法登录");
        // 这里不暴露更多情况。和用户登录的严谨性不同
        return $admin;
    }
    public function getAdminList($page = 1, $page_size = 10)
    {
        $ret = AdminModel::G()->getList($page, $page_size);
        return $ret;
    }
    ///////////////////
    public function add($data)
    {
        $ret = AdminModel::G()->addData($data['username'],$data['nick'],$data['password']);
        return $ret;
    }
    public function checkPermission($admin, $path_info)
    {
        $admin=is_array($admin)?$admin:AdminModel::G()->get($admin);
        ServiceException::ThrowOn(!$admin, '请重新登录');
        // 如果是超级用户，直接返回 true
        // 然后我们查 role 表，看有吗
        return true;
    }
    public function getMenu($admin_id)
    {
        return [];
    }
}