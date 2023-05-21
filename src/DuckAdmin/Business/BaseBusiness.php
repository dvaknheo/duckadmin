<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Business;

use DuckAdmin\System\ProjectBusiness;
use DuckPhp\Core\App;
/**
 * 业务基本类，业务程序员的公用代码放在这里
 */
class BaseBusiness extends ProjectBusiness
{
    protected $exception_class = BusinessException::class;
	
    /**
     * 查询前置
     * @param Request $request
     * @return array
     * @throws BusinessException
     */
    protected function selectInput($data, $table, $dataLimit=null, $dataLimitField =null): array
    {
        $where = $data;
		
        $field = $data['field'] ??null;;
        $order = $data['order']??'asc';
        $format = $data['format']??'normal';
        $page = $data['page']??0;
        $limit = $data['limit']??($format === 'tree' ? 1000 : 10);
		
		$limit = (int)$limit;
        $limit = $limit <= 0 ? 10 : $limit;
        $order = $order === 'asc' ? 'asc' : 'desc';
        $page = $page > 0 ? $page : 1;
		//////////////////////////////////
        
		
		////[[[[

        $allow_column = App::Db()->fetchAll("desc `$table`"); // 这个放到 BaseModel 里。
        $allow_column = array_column($allow_column, 'Field', 'Field');
        if (!in_array($field, $allow_column)) {
            $field = null;
        }
        foreach ($where as $column => $value) {
            if ($value === '' || !isset($allow_column[$column]) ||
                (is_array($value) && (in_array($value[0], ['', 'undefined']) || in_array($value[1], ['', 'undefined'])))) {
                unset($where[$column]);
            }
        }
		if(false){  //-->临时删除
			// 按照数据限制字段返回数据
			if ($dataLimit === 'personal') {
				$where[$dataLimitField] = admin_id();
			} elseif ($dataLimit === 'auth') {
			
				//这里要改
				$primary_key = $this->model->getKeyName();
				// 这里
				if (!Auth::isSupperAdmin() && (!isset($where[$primary_key]) || $dataLimitField != $primary_key)) {
					$where[$dataLimitField] = ['in', Auth::getScopeAdminIds(true)];
				}
			}
		}
		////]]]]
        return [$where, $format, $limit, $field, $order, $page];
    }
	
	/**
     * 格式化数据
     * @param $query
     * @param $format
     * @param $limit
     * @return Response
     */
    protected function doFormat($items,$total,$format, $limit)
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
    protected function formatSelect($items)
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
}
