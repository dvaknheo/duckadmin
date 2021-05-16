<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Controller;
use DuckAdmin\App\ControllerHelper as C;
use DuckAdmin\Service\AdminService;

class Admin extends BaseController
{
    /**
     * 管理员
     */
    public function index()
    {
        $data = AdminService::G()->getAdminList(C::PageNo(),C::PageSize());
        
        C::Show($data);
    }


    /**
     * 添加
     */
    public function add()
    {
        $post = C::Post();
        $result = $post ? AdminService::G()->addAdmin($post) : [];
        //C::SetSuccessMsg('添加成功');
        C::Show([]);
    }

     /**
     * 编辑
     */
    public function edit()
    {
        $admin =  AdminService::G()->getAdmin(C::GET('id'));
        $roles =  AdminService::G()->getRoles();
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
