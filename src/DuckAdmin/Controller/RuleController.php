<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Controller;

use DuckAdmin\System\ProjectController;
use DuckAdmin\System\ControllerHelper as C;
use DuckAdmin\Business\RuleBusiness;

/**
 * 权限菜单
 */
class RuleController extends Base
{
    /**
     * 不需要权限的方法
     *
     * @var string[]
     */
    protected $noNeedAuth = ['get', 'permission'];


    /**
     * 浏览
     * @return Response
     */
    public function index()
    {
        return view('rule/index');
    }

    /**
     * 查询
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function select()
    {
        C::ThrowOn(true,"No Impelement");
    }

    /**
     * 获取菜单
     * @param Request $request
     * @return Response
     */
    function get()
    {
		$types = C::GET('type', '0,1');
        $types = is_string($types) ? explode(',', $types) : [0, 1];
		
		$admin = AdminSession::G()->getCurrentAdmin();
		$data = RuleBusiness::G()->get($admin['roles'],$types);
		
		C::Success($data);

    }

    /**
     * 获取权限
     * @param Request $request
     * @return Response
     */
    public function permission()
    {
        C::ThrowOn(true,"No Impelement");
    }


    /**
     * 添加
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function insert()
    {
		C::ThrowOn(true,"No Impelement");
    }

    /**
     * 更新
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function update()
    {
		C::ThrowOn(true,"No Impelement");
    }
    
    /**
     * 删除
     * @param Request $request
     * @return Response
     */
    public function delete()
    {
		C::ThrowOn(true,"No Impelement");
    }
}
