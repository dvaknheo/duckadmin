<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Controller;

use DuckAdmin\Controller\AdminAction as C;

/**
 * 系统设置
 */
class RoleController extends Base
{
    /**
     * 不需要鉴权的方法
     * @var array
     */
    protected $noNeedAuth = ['select'];

    /**
     * @var Role
     */
    protected $model = null;
	
    /**
     * 浏览
     * @return Response
     */
    public function index()
    {
		C::ThrowOn(true,"No Impelement");
        //return view('role/index');
    }

    /**
     * 查询
     * @param 
     * @return Response
     * @throws BusinessException
     */
    public function select()
    {
		C::ThrowOn(true,"No Impelement");
    }

    /**
     * 插入
     * @param 
     * @return Response
     * @throws BusinessException
     */
    public function insert()
    {
		C::ThrowOn(true,"No Impelement");
    }

    /**
     * 更新
     * @param 
     * @return Response
     * @throws BusinessException
     */
    public function update()
    {
		C::ThrowOn(true,"No Impelement");
    }

    /**
     * 删除
     * @param 
     * @return Response
     * @throws BusinessException
     */
    public function delete()
    {
		C::ThrowOn(true,"No Impelement");
    }

    /**
     * 获取角色权限
     * @param 
     * @return Response
     */
    public function rules()
    {
		C::ThrowOn(true,"No Impelement");
        $role_id = $request->get('id');
        if (empty($role_id)) {
            return $this->json(0, 'ok', []);
        }
        if (!Auth::isSupperAdmin() && !in_array($role_id, Auth::getScopeRoleIds(true))) {
            return $this->json(1, '角色组超出权限范围');
        }
        $rule_id_string = Role::where('id', $role_id)->value('rules');
        if ($rule_id_string === '') {
            return $this->json(0, 'ok', []);
        }
        $rules = Rule::get();
        $include = [];
        if ($rule_id_string !== '*') {
            $include = explode(',', $rule_id_string);
        }
        $items = [];
        foreach ($rules as $item) {
            $items[] = [
                'name' => $item->title ?? $item->name ?? $item->id,
                'value' => (string)$item->id,
                'id' => $item->id,
                'pid' => $item->pid,
            ];
        }
        $tree = new Tree($items);
        return $this->json(0, 'ok', $tree->getTree($include));
    }
}
