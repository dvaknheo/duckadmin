<?php declare(strict_types=1);
/**
 * DuckPhp Admin System - Controller Base
 * 所有后台认证页面的基类
 */
namespace DuckAdmin\Admin\Controller;

use DuckPhp\Foundation\ControllerTrait;

class Base
{
    use ControllerTrait;
    
    protected $user;
    protected $menus;
    
    public function __construct()
    {
        $this->checkLogin();
        $this->user = [
            'id' => Session::_()->getUserId(),
            'username' => Session::_()->getUsername(),
            'realname' => Session::_()->getRealname(),
        ];
        $this->menus = $this->loadMenus();
        // 统一设置页眉页脚
        Helper::setViewHeadFoot('admin/header', 'admin/footer');
    }
    
    /**
     * 检查用户是否已登录
     */
    protected function checkLogin(): void
    {
        if (!Session::_()->isLogin()) {
            Helper::Show302(__url('login/login'));
        }
    }
    
    /**
     * 加载菜单配置
     */
    protected function loadMenus(): array
    {
        $config = Helper::Config('app','menus',[]);
        return $config;
    }
    
    /**
     * 渲染后台页面
     */
    protected function render(string $view, array $data = []): void
    {
        $data['current_user'] = $this->user;
        $data['menus'] = $this->menus;
        $data['app_name'] = 'Admin System';
        Helper::Show($data, $view);
    }
}
