<?php
namespace DuckAdmin\Business;

/**
 * 个人资料业务
 */
class AccountBusiness extends BaseBusiness 
{
	public function getAccountInfo()
	{
		$data = json_decode(file_get_contents(__DIR__.'/data/account.json'),true);
		return $data;
		
		$admin = admin();
        if (!$admin) {
            return $this->json(1);
        }
        $info = [
            'id' => $admin['id'],
            'username' => $admin['username'],
            'nickname' => $admin['nickname'],
            'avatar' => $admin['avatar'],
            'email' => $admin['email'],
            'mobile' => $admin['mobile'],
            'isSupperAdmin' => Auth::isSupperAdmin(),
            'token' => $request->sessionId(),
        ];
        return $this->json(0, 'ok', $info);
	}
}