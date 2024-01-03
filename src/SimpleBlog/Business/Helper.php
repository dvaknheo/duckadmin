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
    public function getUsername($user_id)
    {
        $names = GlobalUser::_()->getUsernames([$user_id]);
        return $names[$user_id]??'--';
    }
    public function getUserNames($user_ids)
    {
        try{
            return GlobalUser::_()->getUsernames($user_ids);
        }catch(\Exception $ex){
            throw $ex;
            // 这里是否要复位？
        }
        
        return [];
    }
}
