<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Controller;

use DuckAdmin\Controller\AdminAction as C;
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
        C::Show([], 'rule/index');
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
		
		$data = RuleBusiness::G()->selectRules();
        
		C::Success($data);
    }

    /**
     * 获取菜单
     * @param Request $request
     * @return Response
     */
    public function get()
    {
		$types = C::GET('type', '0,1');
        $types = is_string($types) ? explode(',', $types) : [0, 1];
		
		$admin = AdminAction::G()->getCurrentAdmin();
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
		$admin = AdminAction::G()->getCurrentAdmin();
        $permissions = RuleBusiness::G()->permissions($admin['roles']);
        C::Success($permissions);
	}
    /**
     * 添加
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function insert()
    {
        if (!C::POST()) {
            C::Show([], 'rule/insert');
        }

		RuleBusiness::G()->insertRule();
        C::Success();
    }

    /**
     * 更新
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function update()
    {
		if (!C::POST()) {
            C::Show([], 'rule/update');
        }
		
		RuleBusiness::G()->updateRule();
        C::Success();
    }
    
    /**
     * 删除
     * @param Request $request
     * @return Response
     */
    public function delete()
    {
		RuleBusiness::G()->deleteRule();
        C::Success();
    }

}
