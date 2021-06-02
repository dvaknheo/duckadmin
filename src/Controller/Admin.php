<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Controller;
use DuckAdmin\Service\AdminService;

class Admin extends BaseController
{
    /**
     * 管理员
     */
    public function index()
    {
        $data = AdminService::G()->getAdminList(C::PageNo(),C::PageSize());
        $roles = AdminService::G()->getRoles();
        $data['roles'] = $roles;
        C::Show($data);
    }


    /**
     * 添加
     */
    public function add()
    {
        $post = C::Post();
        if($post){
            AdminService::G()->addAdmin($post);
            C::ExitRouteTo('Admin/index');
        }
        $roles = AdminService::G()->getRoles();
        $data=[
            'roles' => $roles,
        ];
        C::Show($data);
    }

     /**
     * 编辑
     */
    public function edit()
    {
        $post = C::Post();
        if($post){
            AdminService::G()->updateAdmin($post);
            C::ExitRouteTo('Admin/index');
        }
        $admin = AdminService::G()->getAdmin(C::GET('id'));
        $roles = AdminService::G()->getRoles();
        $data=[
            'admin' => $admin,
            'roles' => $roles,
        ];
        C::Show($data);
    }
    public function log()
    {
        C::Show();
    }
}
