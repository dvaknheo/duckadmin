<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */
namespace DuckAdminDemo\Controller;

use DuckPhp\Foundation\Controller\ControllerHelper as Helper;
class MainController
{
    public function index()
    {
        $data = [];
        //$data['url_blog'] = __url(\DuckAdmin\SimpleBlog\System\SimpleBlogApp::_()->options['controller_url_prefix']) . 'index';
        $data['url_user'] = __url(\DuckAdmin\User\System\UserApp::_()->options['controller_url_prefix']) . 'index';
        $data['url_admin'] = __url(\DuckAdmin\Admin\System\AdminApp::_()->options['controller_url_prefix']) . 'index';
        $data['url_blog'] ='';
        Helper::Show($data,'main');

        //$str = \DuckCoverage\DuckCoverage::_()->genTestListOfAll();
        //echo $str;

    }
}