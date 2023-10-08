<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace DuckAdmin\Controller;

use DuckPhp\Component\SessionBase;
/**
 * 会话处理
 */
class AdminSession extends SessionBase
{
    public function getCurrentAdmin()
    {
        $ret = $this->get('admin', []);
        return $ret;
    }
    public function getCurrentAdminId()
    {
        $user = $this->getCurrentAdmin();
        return $user['id']??0;
    }
    
    public function setCurrentAdmin($admin)
    {
        $this->set('admin', $admin);
    }
	/////////////
    public function getPhrase()
    {
        return $this->get('phrase', '');
    }
    public function setPhrase($phrase)
    {
        return $this->set('phrase', $phrase);
    }
}
