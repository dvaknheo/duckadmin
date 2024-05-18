<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Controller;

use DuckPhp\Helper\ControllerHelperTrait;

use DuckAdmin\Business\AccountBusiness;
use DuckAdmin\Controller\AdminSession;

class AdminAction
{
    use ControllerHelperTrait;
    
    public function getCurrentAdminId()
    {
        $admin = $this->getCurrentAdmin();
        Helper::ControllerThrowOn(!$admin,"需要登录",401);
        return $admin['id'];
    }
    /**
     * 当前管理员
     * @param null|array|string $fields
     * @return array|mixed|null
     */
    public function getCurrentAdmin()
    {
        return AdminSession::_()->getCurrentAdmin();
    }
    public function setCurrentAdmin($admin)
    {
        return AdminSession::_()->setCurrentAdmin($admin);
    }
    public function getAdminIdBySession()
    {
        return AdminSession::_()->getCurrentAdminId();
    }

    ////////////////
    public function checkAccess($controller,$action)
    {
        try{
            $admin_id =AdminSession::_()->getCurrentAdminId();
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
    }
    protected function isOptionsMethod()
    {
        return Helper::SERVER('REQUEST_METHOD','GET')==='OPTIONS'?true:false;
    }
    protected function exit401()
    {
        $response = <<<EOF
<script>
if (self !== top) {
    parent.location.reload();
}
</script>
EOF;
        Helper::header('Unauthorized',true,401);
        echo $response;
        Helper::exit();
    } // @codeCoverageIgnore
    protected function exit403()
    {
        Helper::header('Forbidden',true,403);
        Helper::Show('_sys/403');
        Helper::exit();
    }
    public function doShowCaptcha()
    {
        return CaptchaAction::_()->init([
            'set_phrase_handler'=>[AdminSession::_(),'setPhrase'],
            'get_phrase_handler'=>[AdminSession::_(),'getPhrase'],
        ])->doShowCaptcha();
    }
    public function doCheckCaptcha($captcha)
    {
        return CaptchaAction::_()->init([
            'set_phrase_handler'=>[AdminSession::_(),'setPhrase'],
            'get_phrase_handler'=>[AdminSession::_(),'getPhrase'],
        ])->doCheckCaptcha($captcha);
    }
}