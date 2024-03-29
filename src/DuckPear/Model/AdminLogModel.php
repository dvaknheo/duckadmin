<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckPear\Model;
/**
 * 管理员日志
 */
class AdminLogModel extends BaseModel
{
    // 管理员日志记录
    public function record()
    {
    
        return;
        if(!$desc = Request::except(['s','_pjax']))return;
        if(isset($desc['page'])&&isset($desc['limit']))return;
        foreach ($desc as $k => $v) {
            if(stripos($k, 'fresh') !== false) return;
            if (is_string($v) && strlen($v) > 255 || stripos($k, 'password') !== false)  {
                unset($desc[$k]);
            }
        }
        $info = [
           'uid'       => Session::get('admin.id'),
           'url'      => Request::url(),
           'desc'    => json_encode($desc), 
           'ip'       => Request::ip(),
           'user_agent'=> Request::server('HTTP_USER_AGENT')
        ];
        $res = self::where('uid',$info['uid'])
            ->order('id', 'desc')
            ->find();
        if (isset($res['url'])!==$info['url']) {
            self::create($info);
        }
    }
}
