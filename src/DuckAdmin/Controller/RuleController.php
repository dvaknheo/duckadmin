<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Controller;

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
        Helper::Show([], 'rule/index');
    }

    /**
     * 查询
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function select()
    {
		$data = Helper::GET();

		[$data,$total] = RuleBusiness::_()->selectRules(Helper::AdminId(), $data); // 结果还是一股脑把参数传进去了

		return Helper::Success($data,$total);
    }

    /**
     * 获取菜单
     * @param Request $request
     * @return Response
     */
    public function get()
    {
		$types = Helper::GET('type', '0,1');
        $types = is_string($types) ? explode(',', $types) : [0, 1];
		
		$admin = AdminAction::_()->getCurrentAdmin();
		$data = RuleBusiness::_()->get($admin['roles']??[],$types);
		
		return Helper::Success($data);
    }
    /**
     * 获取权限
     * @param Request $request
     * @return Response
     */
    public function permission()
    {
		$admin = AdminAction::_()->getCurrentAdmin();
        $permissions = RuleBusiness::_()->permission($admin['roles']??[]);
        return Helper::Success($permissions);
	}
    /**
     * 添加
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function insert()
    {
		$post = Helper::POST();
        if (!$post) {
            return Helper::Show([], 'rule/insert');
        }
		RuleBusiness::_()->insertRule(Helper::AdminId(), $post);
        return Helper::Success();
    }

    /**
     * 更新
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function update()
    {
		$post = Helper::POST();
        if (!$post) {
			return Helper::Show([], 'rule/update');
        }
		RuleBusiness::_()->updateRule(Helper::AdminId(), $post);
        return Helper::Success();
    }
    
    /**
     * 删除
     * @param Request $request
     * @return Response
     */
    public function delete()
    {
		$post = Helper::POST();
		RuleBusiness::_()->deleteRule(Helper::AdminId(),$post['id']);
        return Helper::Success();
    }

}
