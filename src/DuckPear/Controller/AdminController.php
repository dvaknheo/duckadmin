<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckPear\Controller;

use DuckPear\Controller\Base as C;
use DuckPear\Business\AdminBusiness;
/**
 * 管理员管理系列页面
 */
class AdminController extends Base
{
    /**
     * 管理员列表
     */
    public function index()
    {
        $data = AdminBusiness::G()->getAdminList(C::PageNo(),C::PageSize());
        $roles = AdminBusiness::G()->getRoles();
        $data['roles'] = $roles;
        C::Show($data);
    }
    /**
     * 添加管理员
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
     * 编辑管理员
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
    /**
     * 日志列表
     */
    public function log()
    {
        C::Show([]);
    }
}
