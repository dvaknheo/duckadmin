<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */
namespace DuckAdmin\System;

use DuckPhp\GlobalAdmin\GlobalAdmin;

use DuckAdmin\Controller\AdminAction;
use DuckAdmin\Business\RuleBusiness;

class Admin extends GlobalAdmin
{
    public $options = [
        'admin_url_home' => '',
        'admin_url_login' => 'login',
        'admin_url_logout' => 'logout',
        
        'admin_view_file_header' =>  '_sys/header',
        'admin_view_file_footer' =>  '_sys/footer',
        
        'admin_enable_callback_singleton' => true,
        'admin_callback_for_id' =>  [AdminAction::class,'id'],
        'admin_callback_for_name' =>[AdminAction::class,'name'],
        'admin_callback_for_data' => null, //[AdminAction::class,'data'],
        'admin_callback_for_local_service' => null, //[AdminAction::class,'service'],
        'admin_callback_for_add_ext_view_data' => null, //[AdminAction::class,'mergeViewData'],

        'admin_callback_for_url_for_home' => null,
        'admin_callback_for_url_for_login' => null,
        'admin_callback_for_url_for_logout' => null,
    ];
    public function addExtViewData(array $input): array
    {
        $admin_id = AdminAction::_()->id();
        $types = [0, 1];
        $input['data_menu'] = RuleBusiness::_()->get($admin_id,$types);
        return $input;
    }
}
