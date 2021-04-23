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
        //ServiceException::ThrowOn(!$flag, "密码错误");
        //BaseService::FireEvent(static::class '::'.. __FUNCTION__,$admin);
        return $admin;
    }
    ///////////////////
    public function reg()
    {
        //
    }
}