<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Controller;
use DuckPhp\Core\App;

use DuckAdmin\Business\InstallBusiness;

/**
 * 主入口
 */
class MainController extends Base
{
    /**
     * 跳过验证，这里
     */
    public function __construct()
    {
        // 跳过验证，不做任何事
    }
    /**
     * 首页
     */
    public function index()
    {
        $admin_id = AdminAction::_()->getAdminIdBySession();
        if (!$admin_id) {
            $url_back = Helper::GET('back_url','');
            $url_back = $url_back === '/' ? '': $url_back;
            if($url_back){
                $last_phase = App::Phase(App::Root()->getOverridingClass());
                $url_back = __url(ltrim($url_back,'/'));
                App::Phase($last_phase);
            }
            Helper::Show(['url_back'=>$url_back], 'account/login');
            return;
        }
        Helper::Show([], 'index/index');
    }
}
