<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckAdmin\User\Controller;

use DuckPhp\GlobalUser\GlobalUser;

class MainController
{
    public function __construct()
    {
        $this->initController();
    }
    protected function initController()
    {
        Helper::checkInstall();
    }
    public function index()
    {
        $data =[];
        $data['url_reg'] = Helper::User()->urlForRegister();
        $data['url_login'] = Helper::User()->urlForLogin();
        
        Helper::Show($data, 'main');
    }
    public function register()
    {
        $data = [];
        $data['url_register'] = Helper::User()->urlForRegister();

        if (Helper::IsPOST()) {
            try {
                GlobalUser::_()->register(Helper::Post());
            return;
            } catch (\Exception $ex) {
                $data['error'] = $ex->getMessage();
            }
        }
        $data['csrf_field'] = Helper::_()->csrfField();
        $data['name'] = __h(Helper::POST('name', ''));

        Helper::Show($data, 'register');
    }
    public function login()
    {
        $data = [];
        if (Helper::IsPOST()) {
            try {
                GlobalUser::_()->login(Helper::POST());
                return;
            } catch (\Exception $ex) {
                $data['error'] = $ex->getMessage();
            }
        }
        $data['url_login'] = Helper::User()->urlForLogin();
        $data['csrf_field'] = Helper::_()->csrfField();
        $data['name'] = __h(Helper::POST('name', ''));
        $data['back_url'] =Helper::GET('b','');

        Helper::Show($data, 'login');
    }
    public function logout()
    {
        GlobalUser::_()->logout();
    }
}
