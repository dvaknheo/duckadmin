<?php declare(strict_types=1);
/**
 * DuckPhp Admin System - Dashboard Controller
 */
namespace DuckAdmin\Admin\Controller;

use DuckAdmin\Admin\Business\HomeBusiness;

/**
 * 
 * @menu_directory 我的主页
 * @menu_weight 99

 */
class HomeController extends Base
{
    /**
     * @menu 仪表盘
     */
    public function index()
    {
        $data = [];
        Helper::Show($data, 'Home/dashboard');
    }

    /**
     * @menu 个人信息
     */
    public function profile()
    {
        $userId = (int)Session::_()->getCurrentAdminId();

        if (Helper::SERVER('REQUEST_METHOD') === 'POST') {
            return $this->doUpdate($userId);
        }

        $data['user'] = HomeBusiness::_()->getUser($userId);
        $data['title'] = '个人信息';
        Helper::Show($data, 'Home/profile');
    }

    /**
     * 更新个人信息和密码
     */
    protected function doUpdate(int $userId)
    {
        $realname = Helper::POST('realname', '');
        $email = Helper::POST('email', '');
        $password = Helper::POST('password', '');

        try {
            HomeBusiness::_()->updateProfile($userId, $realname, $email, $password);
            Helper::Show302(__url('Home/profile') . '?updated=1');
        } catch (\Exception $ex) {
            $data['error'] = $ex->getMessage();
        }

        $data['user'] = HomeBusiness::_()->getUser($userId);
        $data['title'] = '个人信息';
        Helper::Show($data, 'Home/profile');
    }

    /**
     * @menu 查看权限
     */
    public function permissions()
    {
        $userId = (int)Session::_()->getCurrentAdminId();
        $data = HomeBusiness::_()->getUserPermissionTree($userId);
        $data['title'] = '我的权限';
        Helper::Show($data, 'Home/permissions');
    }
    /**
     * @menu 查看菜单
     */
    public function menu()
    {
        $userId = (int)Session::_()->getCurrentAdminId();
        $data = HomeBusiness::_()->getUserMenuTree($userId);
        $data['title'] = '我的菜单';
        Helper::Show($data, 'Home/menu');
    }
}
