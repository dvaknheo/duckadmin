<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckAdmin\User\Controller;

use DuckPhp\Core\App;
use DuckAdmin\User\Business\UserBusiness;
use DuckAdmin\User\Business\InstallBusiness;

class MainController extends Base
{
    public function __construct()
    {
        // this override for skip auth
        $method = Helper::getRouteCallingMethod();
        if ($method !== 'install') {
            Helper::checkInstall('install');
        }
    }
    /**
     * DuckUser 安装：GET 展示环境自检与安装按钮；POST 执行安装
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
            Helper::Show($data, 'install');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$allOk) {
                $data['error'] = '环境自检未通过，请先解决上述问题后再安装';
                Helper::Show($data, 'install');
                return;
            }
            $result = InstallBusiness::_()->install();
            if (!$result['ok']) {
                $data['error'] = $result['error'];
                Helper::Show($data, 'install');
                return;
            }
            $data['done'] = true;
            $data['url_home'] = Helper::Url('');
            Helper::Show($data, 'install');
            return;
        }

        Helper::Show($data, 'install');
    }
    public function index()
    {
        $url_reg = Helper::User()->urlForRegist();
        $url_login = Helper::User()->urlForLogin();
        
        Helper::Show(get_defined_vars(), 'main');
    }
    public function register()
    {
        $post = Helper::POST();
        
        if (!$post) {
            $csrf_field = Helper::_()->csrfField();
            $url_register = Helper::User()->urlForRegist();
            
            Helper::Show(get_defined_vars(), 'register');
            return;
        }
        try {
            $user = UserAction::_()->regist($post);
            Helper::_()->goHome();
        } catch (\Exception $ex) {
            $error = $ex->getMessage();
            $name = Helper::POST('name', '');
            Helper::Show(get_defined_vars(), 'register');
            return;
        }

    }
    public function login()
    {
        $post = Helper::POST();
        if (!$post) {
            $csrf_field = Helper::_()->csrfField();
            $url_login = Helper::User()->urlForLogin();
            $back_url = Helper::GET('b','');
            Helper::Show(get_defined_vars(),'login');
            return;
        }
        try {
            UserAction::_()->login($post);
            $back_url = Helper::GET('b','');
            
            if(!$back_url){
                Helper::_()->goHome();
            }else{
                $last_phase = App::Phase(App::Root());
                $back_url = __url($back_url);
                Helper::Show302($back_url);
                App::Phase($last_phase);
            }
        } catch (\Exception $ex) {
            $error = $ex->getMessage();
            $name =  __h( Helper::POST('name', ''));
            Helper::Show(get_defined_vars(), 'login');
            return;
        }
    }
    public function logout()
    {
        UserAction::_()->logout();
        
        Helper::Show302('index');
    }
}
