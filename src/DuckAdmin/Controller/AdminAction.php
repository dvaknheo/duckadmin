<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Controller;

use DuckPhp\Helper\ControllerHelperTrait;

use DuckAdmin\Business\AccountBusiness;
use DuckAdmin\Controller\Session;

class AdminAction
{
    use ControllerHelperTrait;
    
    protected $admin = null;
    public function getCurrentAdminId()
    {
        if ($this->admin) {
            return $this->admin['id'];
        }
        $this->admin = $this->getCurrentAdmin();
        Helper::ControllerThrowOn(!$this->admin,"需要登录",401);
        return $admin['id'];
    }
    /**
     * 当前管理员
     * @param null|array|string $fields
     * @return array|mixed|null
     */
    public function getCurrentAdmin()
    {
        return Session::_()->getCurrentAdmin();
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
    public function checkAccess($controller = null, $action = null,$url = null)
    {
        $controller = $controller ?? Helper::getRouteCallingClass();
        $action = $action ?? Helper::getRouteCallingMethod();
        $url = $url ?? Helper::PathInfo();
        
        try{
            $admin_id = Session::_()->getCurrentAdminId();
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
        $url = __url('index');
        $response = <<<EOF
<script>
if (self !== top) {
    parent.location.reload();
}
</script>
<meta http-equiv=refresh content=5;url="$url">
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
}