<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace SimpleBlog\Business;

use DuckPhp\Core\App;
use DuckPhp\Component\GlobalUser;
use DuckPhp\Helper\BusinessHelperTrait;

class Helper
{
    use BusinessHelperTrait;
    public function getUserName($user_id)
    {
        $user_data = GlobalUser::_()->data();
        return $user_data['username'];
    }
    public function getUserNames($user_ids)
    {
        //
    }
}
