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
    /* 没用上 
    protected function formatTableTree($items)
    {
        $tree = new Tree($items);
        return [$tree->getTree(),null];
    }
    */

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
}