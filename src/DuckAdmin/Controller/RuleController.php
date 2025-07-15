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
        // 这里要不要同步权限？
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
        $data['limit']=5000;
        //这里有个同步权限的。
        [$data,$total] = RuleBusiness::_()->selectRules(Helper::AdminId(), $data); // 结果还是一股脑把参数传进去了
        // 这里还要加上 父菜单信息
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
    public function insert()
    {
        $data =[];
        if(Helper::GET('inframe',false)){
            Helper::setViewHeadFoot("_sys/header-layer","_sys/footer-layer");
            $data['inframe']=true;
        }
        return Helper::Show($data,'rule/insert');
    }
    public function do_insert()
    {
        RuleBusiness::_()->insertRule(Helper::AdminId(), Helper::POST());
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
        $data =[];
        if(Helper::GET('inframe',false)){
            Helper::setViewHeadFoot("_sys/header-layer","_sys/footer-layer");
            $data['inframe']=true;
        }
        $data['id'] = intval(Helper::GET('id',0));
        Helper::Show($data, 'rule/update');
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
