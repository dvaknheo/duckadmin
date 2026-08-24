<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckAdmin\User\Controller;

use DuckPhp\Core\App;
use DuckAdmin\User\Business\UserBusiness;
use DuckAdmin\User\Business\InstallBusiness;

class MainController
{
    public function __construct()
    {
        Helper::checkInstall();
    }
    /**
     * DuckUser 安装：GET 展示环境自检与安装按钮；POST 执行安装
     */
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
