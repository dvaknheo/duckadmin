<?php declare(strict_types=1);
/**
 * DuckPhp Admin System - Main Controller
 * 处理首页路由 /
 */
namespace DuckAdmin\Admin\Controller;

use DuckAdmin\Admin\Business\InstallBusiness;

class MainController extends Base
{
    public function __construct()
    {
        // install 页面未安装时无需登录即可访问；其余页面走 Base（登录检查 + 安装检查）
        $method = Helper::getRouteCallingMethod();
        if ($method !== 'install') {
            parent::__construct();
        }
    }
    public function index()
    {
        $this->render('admin/dashboard');
    }
    /**
     * DuckAdmin 安装：GET 展示环境自检与安装按钮；POST 执行安装
     */
    public function install()
    {
        $checks = InstallBusiness::_()->environmentCheck();
        $allOk = true;
        foreach ($checks as $check) {
            if (empty($check['ok'])) {
                $allOk = false;
                break;
            }
        }

        $data = [
            'installed' => InstallBusiness::_()->isInstalled(),
            'done' => false,
            'checks' => $checks,
            'all_ok' => $allOk,
            'error' => '',
        ];

        if ($data['installed']) {
            Helper::Show($data, 'admin/install');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$allOk) {
                $data['error'] = '环境自检未通过，请先解决上述问题后再安装';
                Helper::Show($data, 'admin/install');
                return;
            }
            $result = InstallBusiness::_()->install();
            if (!$result['ok']) {
                $data['error'] = $result['error'];
                Helper::Show($data, 'admin/install');
                return;
            }
            $data['done'] = true;
            $data['url_home'] = Helper::Url('');
            Helper::Show($data, 'admin/install');
            return;
        }

        Helper::Show($data, 'admin/install');
    }
}
