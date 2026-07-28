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
        //
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
    public function checkAccess($class = null, $method = null,$url = null)
    {
        return;
        $controller = $class ?? Helper::getRouteCallingClass();
        $action = $method ?? Helper::getRouteCallingMethod();
        $url = $url ?? Helper::PathInfo();
        
        try{
            //__var_log($_SESSION?? null);
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
    protected function onAuthException($ex)
    {
        if (Helper::IsAjax()) {
            Helper::ShowException($ex);
            Helper::exit();
            return; // @codeCoverageIgnore
        }
        $code = $ex->getCode();
        if($code == 401){
            return $this->exit401();
        }else if($code == 403){
            return $this->exit403();
        }
        Helper::Show302('index');
        Helper::exit();
    } // @codeCoverageIgnore
    protected function isOptionsMethod()
    {
        return Helper::SERVER('REQUEST_METHOD','GET')==='OPTIONS'?true:false;
    }
    protected function exit401()
    {
        $url = __url('index').'?back_url='.Helper::PathInfo();
        $response = <<<EOF
<script>
if (self !== top) {
    parent.location.reload();
}
</script>
<meta http-equiv=refresh content=3;url="$url">
EOF;
 
        Helper::header('Unauthorized',true,401);
        echo $response;
        Helper::exit();
    } // @codeCoverageIgnore
    protected function exit403()
    {
        Helper::header('Forbidden',true,403);
        Helper::Show([], '_sys/error_403');
        Helper::exit();
    } // @codeCoverageIgnore

    /////////////////
    //@override

    //@override
    public function id($check_login = true):int
    {
        if($check_login){
            return (int)$this->getCurrentAdmin()['id'];
        }
        try{
            return (int)$this->getCurrentAdmin()['id'];
        }catch(\Exception $ex){
            return 0;
        }
    }
    //@override
    public function name($check_login = true):string
    {
        if($check_login){
            return $this->getCurrentAdmin()['username'];
        }
        try{
            return $this->getCurrentAdmin()['username'];
        }catch(\Exception $ex){
            return '';
        }
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
    
    public function addExtViewData(array $input): array
    {
        $user = [
            'id' => Session::_()->getUserId(),
            'username' => Session::_()->getUsername(),
            'realname' => Session::_()->getRealname(),
        ];
        $input['menus'] = $this->loadMenus();
        $input['current_user'] = $user;
        return $input;
    }

}