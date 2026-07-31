<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Admin\Controller;

use DuckPhp\Core\SingletonTrait;
use DuckPhp\Core\App;

use DuckAdmin\Admin\Business\AdminBusiness;

class AdminAction extends Base
{
    public function __construct()
    {
        // override parent
    }
    
    protected $admin = null;
    
    /**
     * 当前管理员
     * @param null|array|string $fields
     * @return array|mixed|null
     */
    protected function getCurrentAdmin()
    {
        
        if ($this->admin) {
            return $this->admin;
        }
        $this->admin =[
            'id' => Session::_()->getUserId() ??null,
            'name' =>Session::_()->getUsername() ??null,
        ];
        
        if(!$this->admin['id']){
        
            Helper::ControllerThrowOn(!$this->admin,"需要登录",401);
            $admin =Helper::Admin();
            $url = Helper::Admin()->urlForLogin();
            Helper::Show302($url);
            Helper::exit();
        }
        
        return $this->admin;
    }
    public function setCurrentAdmin($admin)
    {
        return Session::_()->setCurrentAdmin($admin);
    }
    public function getAdminIdBySession()
    {
        return Session::_()->getCurrentAdminId();
    }

    ////////////////

    protected function onAuthException($ex)
    {
        if (Helper::IsAjax()) {
            Helper::ShowException($ex);
            Helper::exit();
            return; // @codeCoverageIgnore
        }
        Helper::Show302('index');
        Helper::exit();
    } // @codeCoverageIgnore
    protected function isOptionsMethod()
    {
        return Helper::SERVER('REQUEST_METHOD','GET')==='OPTIONS'?true:false;
    }
    /////////////////
    //@override
    public function id($check_login = true):int
    {
        $ret = Session::_()->getUserId() ?? 0;
        Helper::ControllerThrowOn( $check_login && !$ret, "No Login");
        return $ret;
    }
    //@override
    public function name($check_login = true):string
    {
        $ret = Session::_()->getUsername() ?? 0;
        Helper::ControllerThrowOn( $check_login && !$ret, "No Login");

        return $ret;
    }
    public function data()
    {
        return [];
    }
    //@override
    public function localService()
    {
        return AdminBusiness::_();
    }

    public function login(array $post)
    {
        throw new \Exception('no implement');
    }
    public function logout()
    {
        $this->admin = [];
        Session::_()->setCurrentAdmin([]);
    }
    
    //@override
    public function addExtViewData(array $input): array
    {
        $input['app_name'] = 'Admin System';
        
        $user = [
            'id' => Session::_()->getUserId(),
            'username' => Session::_()->getUsername(),
            'realname' => Session::_()->getRealname(),
        ];
        $input['menus'] = $this->loadMenus();
        $input['current_user'] = $user;
        return $input;
    }
    public function checkAccess($class = null, $method = null,$url = null)
    {
        try{
            
            $admin_id = Session::_()->getCurrentAdminId();
            $admin_id = $admin_id ? $admin_id :0;
            AccountBusiness::_()->canAccess($admin_id, $controller, $action);
        } catch(\Exception $ex) {
            $this->onAuthException($ex);
            return; // @codeCoverageIgnore
        }
        $flag = $this->isOptionsMethod();
        if($flag){
            Helper::exit();
            return; // @codeCoverageIgnore
        }
        return;
    }
}