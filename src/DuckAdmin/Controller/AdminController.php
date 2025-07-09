<?php
namespace DuckAdmin\Controller;

use DuckAdmin\Business\AdminBusiness;

/**
 * 管理员列表 
 */
class AdminController extends Base
{
    /**
     * 不需要鉴权的方法
     * @var array
     */
    protected $noNeedAuth = ['select'];

    /**
     * 浏览
     * @return Response
     */
    public function index()
    {
        return Helper::Show([],'admin/index');
    }
    public function select()
    {
        $input = Helper::GET();
        [$data, $count] = AdminBusiness::_()->showAdmins(Helper::AdminId(),$input);
        return Helper::Success($data,$count);
    }
    public function insert()
    {
        $data =[];
        if(Helper::GET('inframe',false)){
            Helper::setViewHeadFoot("_sys/header-layer","_sys/footer-layer");
            $data['inframe']=true;
        }
        return Helper::Show($data,'admin/insert');
    }
    public function do_insert()
    {
        $input = Helper::POST();
        $admin_id = AdminBusiness::_()->addAdmin(Helper::AdminId(), $input);
        return Helper::Success(['id' => $admin_id]);
    }
    public function update()
    {
        $data =[];
        if(Helper::GET('inframe',false)){
            Helper::setViewHeadFoot("_sys/header-layer","_sys/footer-layer");
            $data['inframe']=true;
        }
        return Helper::Show($data,'admin/update');
    }
    public function do_update()
    {
        $post = Helper::POST();
        AdminBusiness::_()->updateAdmin(Helper::AdminId(), $post);
        return Helper::Success();
    }
    public function delete()
    {
        AdminBusiness::_()->deleteAdmin(Helper::AdminId(), Helper::POST('id',null));
        return Helper::Success();
    }

}
