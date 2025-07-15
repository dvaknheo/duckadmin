<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Controller;

use DuckAdmin\Business\AccountBusiness;

/**
 * 系统设置
 */
class AccountController extends Base
{

    /**
     * 不需要鉴权的方法
     * @var string[]
     */
    protected $noNeedAuth = ['info','dashboard','permission'];

    /**
     * 账户设置
     * @return Response
     */
    public function index()
    {
        return Helper::Show([],'account/index');
    }

    /**
     * 获取登录信息
     * @param 
     */
    public function info()
    {
        $admin_id = Helper::AdminId();
        $data = AccountBusiness::_()->getAccountInfo($admin_id);
        
        return Helper::Success($data);
    }
    public function dashboard()
    {
        $data = AccountBusiness::_()->getDashBoardInfo(Helper::AdminId());
        Helper::Show($data, 'index/dashboard');
    }
    /**
     * 获取权限
     * @param Request $request
     * @return Response
     */
    public function permission()
    {
        //这里是动态的获取权限。
        $admin_id = Helper::AdminId();
        $permissions = RuleBusiness::_()->permission($admin_id);
        return Helper::Success($permissions);
    }
    /**
     * 更新
     * @param 
     * @return Response
     */
    public function update()
    {
        $admin = AccountBusiness::_()->update(Helper::AdminId(),Helper::POST());
        AdminAction::_()->setCurrentAdmin($admin);
        
        Helper::Success();
    }

    /**
     * 修改密码
     * @param 
     * @return Response
     */
    public function password()
    {
        if(!Helper::POST()){
            return;
        }
        $password = Helper::POST('password');
        $password_confirm = Helper::POST('password_confirm');
        $old_password = Helper::POST('old_password');
        
        AccountBusiness::_()->changePassword(Helper::AdminId(), $old_password, $password, $password_confirm );
        Helper::Success();
    }

}
