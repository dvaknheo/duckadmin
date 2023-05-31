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
        return C::Show([], 'rule/index');
    }

    /**
     * 查询
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function select()
    {
		$data = C::GET();
		
		$admin_id = AdminAction::G()->getCurrentAdminId();
		[$data,$total] = RuleBusiness::G()->selectRules($admin_id, $data); // 结果还是一股脑把参数传进去了
		return C::Success($data,$total);
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
		$admin_id = AdminAction::G()->getCurrentAdminId();

		$data = RuleBusiness::G()->get($admin['roles'],$types);
		
		return C::Success($data);
    }
    /**
     * 获取权限
     * @param Request $request
     * @return Response
     */
    public function permission()
    {
		$admin = AdminAction::G()->getCurrentAdmin();
        $permissions = RuleBusiness::G()->permission($admin['roles']);
        return C::Success($permissions);
	}
    /**
     * 添加
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function insert()
    {
		$post = C::POST();
        if (!$post) {
            return C::Show([], 'rule/insert');
        }
		$admin_id = AdminAction::G()->getCurrentAdminId();
		RuleBusiness::G()->insertRule($admin_id, $post);
        return C::Success();
    }

    /**
     * 更新
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function update()
    {
		$post = C::POST();
        if (!$post) {
			return C::Show([], 'rule/update');
        }
		$admin_id = AdminAction::G()->getCurrentAdminId();
		RuleBusiness::G()->updateRule($admin_id, $post);
        return C::Success();
    }
    
    /**
     * 删除
     * @param Request $request
     * @return Response
     */
    public function delete()
    {
		$post = C::POST();
		
		$admin_id = AdminAction::G()->getCurrentAdminId();
		RuleBusiness::G()->deleteRule($admin_id,$post['id']);
        return C::Success();
    }

}
