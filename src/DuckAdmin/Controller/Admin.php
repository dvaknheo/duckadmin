<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Controller;

use DuckAdmin\Controller\Base as C;
use DuckAdmin\Business\AdminBusiness;
/**
 * 管理员
 */
class Admin extends Base
{
    /**
     * 管理员
     */
    public function index()
    {
        $data = AdminBusiness::G()->getAdminList(C::PageNo(),C::PageSize());
        $roles = AdminBusiness::G()->getRoles();
        $data['roles'] = $roles;
        C::Show($data);
    }

    public function password()
    {
        var_dump("OK");
    }
    /**
     * 添加
     */
    public function add()
    {
        $post = C::Post();
        if($post){
            AdminBusiness::G()->addAdmin($post);
            C::ExitRouteTo('Admin/index');
            return;  // return 是个好习惯。
        }
        $roles = AdminBusiness::G()->getRoles();
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
            AdminBusiness::G()->updateAdmin($post);
            C::ExitRouteTo('Admin/index');
            return; // return 是个好习惯。
        }
        $admin = AdminBusiness::G()->getAdmin(C::GET('id'));
        $roles = AdminBusiness::G()->getRoles();
        $data=[
            'admin' => $admin,
            'roles' => $roles,
        ];
        C::Show($data);
    }
    public function log()
    {
        C::Show([]);
    }
}
