<?php
namespace DuckAdmin\Business;

use DuckAdmin\Model\AdminRoleModel;
use DuckAdmin\Model\RuleModel;
use DuckAdmin\Model\RoleModel;

/**
 * 给各业务做的一些服务
 */
class CommonService extends Base
{
    //////////////////////
    /**
     * 格式化数据
     * @param $query
     * @param $format
     * @param $limit
     * @return Response
     */
    public function doFormat($items,$total,$format, $limit)
    {
        $methods = [
            'select' => 'formatSelect',
            'tree' => 'formatTree',
            'table_tree' => 'formatTableTree',
            'normal' => 'formatNormal',
        ];
        $format_function = $methods[$format] ?? 'formatNormal';
        return call_user_func([$this, $format_function], $items, $total);
    }
    
    /**
     * 格式化树
     * @param $items
     * @return Response
     */
    protected function formatTree($items)
    {
        $format_items = [];
        foreach ($items as $item) {
            $format_items[] = [
                'name' => $item['title'] ?? $item['name'] ?? $item['id'],
                'value' => (string)$item['id'],
                'id' => $item['id'],
                'pid' => $item['pid'],
            ];
        }
        $tree = new Tree($format_items);
        
        return [$tree->getTree(),null];
    }

    /**
     * 格式化表格树
     * @param $items
     * @return Response
     */
    protected function formatTableTree($items)
    {
        $tree = new Tree($items);
        return [$tree->getTree(),null];
    }

    /**
     * 格式化下拉列表
     * @param $items
     * @return Response
     */
    public function formatSelect($items)
    {
        $formatted_items = [];
        foreach ($items as $item) {
            $formatted_items[] = [
                'name' => $item['title'] ?? $item['name'] ?? $item['id'],
                'value' => $item['id']
            ];
        }
        return [$formatted_items,null];
    }
    /**
     * 通用格式化
     * @param $items
     * @param $total
     * @return Response
     */
    protected function formatNormal($items, $total)
    {
        return [$items, $total];
    }
    /////////////////////////////////////////////////////
    public function noRole($admin_id,$role_id,bool $with_self = false)
    {
        if(!$this->isSupperAdmin((int)$admin_id)){
            return false;
        }
        $roles = AdminRoleModel::_()->getRoles($admin_id);

        $role_id=is_array($role_id)?$role_id:[$role_id];
        if(array_diff($role_id, $this->getScopeRoleIds($roles, $with_self))){
            return true;
        }else{
            return false;
        }
    }
    public function isSupperAdmin(int $admin_id = 0): bool
    {
        BusinessException::ThrowOn($admin_id==0,'参数错误，请指定管理员');
        $roles = AdminRoleModel::_()->getRoles($admin_id);
        $rules = RoleModel::_()->getRules($roles);
        return RuleModel::_()->isSuper($rules); 
    }
    /**
     * 获取权限范围内的所有角色id
     * @param bool $with_self
     * @return array
     */
    public function getScopeRoleIds($role_ids,  bool $with_self = false): array
    {
        //$role_ids = $admin['roles'];
        
        $rules = RoleModel::_()->getRules($role_ids);
        if (RuleModel::_()->isSuper($rules)) {
            return RoleModel::_()->getAllId();
        }
        $roles = RoleModel::_()->getAll();
        
        $tree = new Tree($roles);
        $descendants = $tree->getDescendant($role_ids, $with_self);
        return array_column($descendants, 'id');
    }
}