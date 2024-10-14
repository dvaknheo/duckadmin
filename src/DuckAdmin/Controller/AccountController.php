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
     * 不需要登录的方法
     * @var string[]
     */
    protected $noNeedLogin = ['login', 'logout', 'captcha'];

    /**
     * 不需要鉴权的方法
     * @var string[]
     */
    protected $noNeedAuth = ['info','dashboard'];

    /**
     * 账户设置
     * @return Response
     */
    public function index()
    {
        return Helper::Show([],'account/index');
    }

    /**
     * 登录
     * @param 
     * @return Response
     * @throws BusinessException
     */
    public function login()
    {
        if(!Helper::Post()){
            return Helper::Show([],'account/index');
        }
        $username = Helper::Post('username', '');
        $password = Helper::Post('password', '');
        $captcha = Helper::Post('captcha');
        
        //这里有更好判断方法不需要特殊化，TODO 用上
        $flag = AdminAction::_()->doCheckCaptcha($captcha);
        Helper::ControllerThrowOn(!$flag, '验证码错误',1);

        
        $admin = AccountBusiness::_()->login($username, $password);
        AdminAction::_()->setCurrentAdmin($admin);
        return Helper::Success($admin);
    }

    /**
     * 退出
     * @param 
     * @return Response
     */
    public function logout()
    {
        AdminAction::_()->logout();
        if(Helper::IsAjax()){
            Helper::Success(0);
        }else{
            Helper::Show302(__url('index'));
        }
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
        // 这里要验证， 捂脸
        $data = AccountBusiness::_()->getDashBoardInfo(Helper::AdminId());
        Helper::Show($data, 'index/dashboard');
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
    /**
     * 验证码
     * @param 
     * @param string $type
     * @return Response
     */
    public function captcha()
    {
        AdminAction::_()->doShowCaptcha();
    }
}
