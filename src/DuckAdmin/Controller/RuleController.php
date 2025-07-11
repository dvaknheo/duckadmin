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
    protected $noNeedAuth = ['get', 'select','permission'];


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
        
        $admin_id = Helper::AdminId();
        $data = RuleBusiness::_()->get($admin_id,$types);
        
        return Helper::Success($data);
    }
    /**
     * 获取权限
     * @param Request $request
     * @return Response
     */
    public function permission()
    {
        $admin_id = Helper::AdminId();
        $permissions = RuleBusiness::_()->permission($admin_id);
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
        $v = [];
        $v['id'] = intval(Helper::GET('id'));
        //$v['rule_data'] = RuleBusiness::_()->getRuleById($v['id']); // rule/select?id=1;
        //$v['rule_tree'] = RuleBusiness::_()->getRuleTree('0,1');     // rule/select?format=tree&type=0,1
        
        Helper::Show($v, 'rule/update');
    }
    public function do_update()
    {
        RuleBusiness::_()->updateRule(Helper::AdminId(), Helper::POST());
        Helper::Success();
    }
    
    /**
     * 删除
     * @param Request $request
     * @return Response
     */
    public function delete()
    {
        RuleBusiness::_()->deleteRule(Helper::AdminId(),Helper::POST('id',0));
        return Helper::Success();
    }

}
