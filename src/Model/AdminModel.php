<?php
declare (strict_types = 1);

/**
 * @mixin \think\Model
 */
class AdminAdmin extends BaseModel
{
    use SoftDelete;
    /**
     * 用户登录验证
     */
    public function login(array $data):bool
    {
        $token = rand_string(60);
        //验证用户
        $admin = self::where([
            'username' => trim($data['username']),
            'password' => set_password(trim($data['password'])),
            'status' => 1
         ])->find();
         
        if($admin){
            //是否记住密码
            Session::set('admin', [
                'id' => $admin->id,
                'token' => $token,
                'menu' => $this->permissions($admin->id)
            ]);
            if (isset($data['remember'])) {
                Cookie::set('hash', aes_encrypt($admin->id.'###'.$token),30 * 86400);
            }else{
                Cookie::set('hash', aes_encrypt($admin->id.'###'.$token),null);
            }
            $admin->token = $token;
            $admin->save();
            // 触发登录成功事件
            event('AdminLog');
            return true;
        }
        return false;
    }
    
    /**
     * 判断是否登录
     * @return bool
     */
    public function isLogin():bool
    {
        $admin = Session::get('admin');
        $hash = Cookie::get('hash');
        if (!$admin && !$hash) return false;
        //判断Session是否存在
        if (!$admin) {
            $hash = explode('###', aes_decrypt($hash?$hash:'-'));
            if (!isset($hash[1])) return false;
            $info = self::field(true)->where(['id'=>$hash[0],'token'=>$hash[1],'status'=>1])->find();
            if(!$info) return false;
            // 缓存登录信息
            $data = [
                'id' => $info->id,
                'token' => $info->token,
                'menu' => $this->permissions($info->id)
            ];
            Session::set('admin', $data);
            return true;
         }
         //判断Cookie是否存在
         if(!$hash){
            $admin = self::where(['id'=>$admin['id'],'token'=>$admin['token'],'status'=>1])->find();
            if($admin){
                $hash = aes_encrypt($admin->id.'###'.$admin->token);
                Cookie::set('hash', $hash,null);
               return true;
            }
         }
         return true;
    }
}
