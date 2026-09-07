<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Admin\Controller;

use DuckPhp\Foundation\SingletonTrait;
use DuckPhp\Core\App;
use DuckPhp\GlobalUser\UserException;

use DuckAdmin\Admin\Business\AdminBusiness;
use DuckAdmin\Admin\Model\PermissionModel;

class AdminAction
{
    use SingletonTrait;
    public function __construct()
    {
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
        return Session::_()->getUserId();
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
        Helper::assignExceptionHandler(UserException::class, function($ex){
            Helper::Show302(Helper::Admin()->urlForLogin());
            Helper::exit();
        });
        Helper::ControllerThrowOn( $check_login && !$ret, "No Login1",-1, UserException::class);
        return $ret;
            
    }
    //@override
    public function name($check_login = true):string
    {
        $ret = Session::_()->getUsername() ?? 0;
        Helper::ControllerThrowOn( $check_login && !$ret, "No Login2");

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
    
    protected function loadMenus(): array
    {
        $admin_id = (int)Helper::AdminId(false);
        return PermissionModel::_()->getUserMenus($admin_id);
    }

    //@override
    public function checkAccess($class = null, $method = null,$url = null)
    {
        try{
            $admin_id = Session::_()->getUserId();
            $admin_id = $admin_id ? $admin_id : 0;
            $url = $url ?? (string)Helper::SERVER('REQUEST_URI', '');
            if (AdminBusiness::_()->checkAccess($admin_id, $class, $method, $url)) {
                return;
            }
            throw new \Exception('无权访问', 403);
        } catch(\Exception $ex) {
            $this->onAuthException($ex);
            return; // @codeCoverageIgnore
        }
    }
}
