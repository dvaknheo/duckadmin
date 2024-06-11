<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Controller;

use DuckPhp\Component\ZCallTrait;
use DuckPhp\Core\SingletonTrait;
use DuckPhp\Core\App;
use DuckPhp\GlobalAdmin\AdminActionInterface;

use DuckAdmin\Business\AccountBusiness;
use DuckAdmin\Business\AdminService;
use DuckAdmin\Controller\Session;

class AdminAction implements AdminActionInterface
{
    use SingletonTrait;
    use ZCallTrait;
    
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
        $this->admin = Session::_()->getCurrentAdmin();
        Helper::ControllerThrowOn(!$this->admin,"需要登录",401);
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
    public function doShowCaptcha()
    {
        return CaptchaAction::_()->init([
            'set_phrase_handler'=>[Session::_(),'setPhrase'],
            'get_phrase_handler'=>[Session::_(),'getPhrase'],
        ])->doShowCaptcha();
    }
    public function doCheckCaptcha($captcha)
    {
        return CaptchaAction::_()->init([
            'set_phrase_handler'=>[Session::_(),'setPhrase'],
            'get_phrase_handler'=>[Session::_(),'getPhrase'],
        ])->doCheckCaptcha($captcha);
    }
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
    //@override
    public function service()
    {
        return AdminService::_Z();
    }
    //@override
    public function login(array $post)
    {
        throw new \Exception('no implement');
    }
    //@override
    public function logout()
    {
        $this->admin = [];
        Session::_()->setCurrentAdmin([]);
    }
    //@override
    public function isSuper()
    {
        throw new \Exception('todo');
    }
    //@override
    public function urlForLogin($url_back = null, $ext = null)
    {
        return __url("");
    }
    //@override
    public function urlForLogout($url_back = null, $ext = null)
    {
        return __url("account/logout");
    }
    //@override
    public function urlForHome($url_back = null, $ext = null)
    {
        return __url("");
    }
    public function log(string $string, ?string $type = null)
    {
        return;
    }
}