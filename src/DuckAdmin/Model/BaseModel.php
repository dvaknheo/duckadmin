<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Model;

use DuckPhp\Foundation\SimpleModelTrait;
use DuckPhp\Helper\ModelHelperTrait;

class BaseModel
{
    use SimpleModelTrait;
    use ModelHelperTrait;
    
    public function table()
    {
        return $this->table_name;
    }
	
	//这对外
	public function doSelect(array $where, string $field = null, string $order= 'desc' ,$page=1,$page_size=10)
    {
		$sql_where =' TRUE ';
        foreach ($where as $column => $value) {
            if (is_array($value)) {
                if (in_array($value[0], ['>', '=', '<', '<>', 'like', 'not like'])) {
					$sql_where.=" and `$column` ". $value[0] . self::Db()->quote($value[1]);
                } elseif ($value[0] == 'in') {
					$sql_where.=" and `$column` in(". self::Db()->quoteIn($value[1]).")";
                } elseif ($value[0] == 'not in') {
					$sql_where.=" and `$column` not in(". self::Db()->quoteIn($value[1]).")";
                } elseif ($value[0] == 'null') {
					$sql_where.=" and `$column`  is null";
                } elseif ($value[0] == 'not null') {
					$sql_where.=" and `$column`  not null";
                } else {
					$sql_where.="`$column`  between(".self::Db()->quoteIn($value[1]).")";
                }
            } else {
				$sql_where.=" and `$column` = ". self::Db()->quote($value);
            }
        }
		// 我们老土的用 sql  语句来完成代码
		$sql = "select * from `".$this->table()."` where $sql_where";

		$total = self::Db()->fetchColumn(self::SqlForCountSimply($sql));
		if ($field) {
			$sql .=" order by `$field` $order "; // 这里可能会有些问题
		}
		$items = self::Db()->fetchAll(self::SqlForPager($sql, $page, $page_size));
        return [$items, $total];
    }
	
	/**
     * 对用户输入表单过滤
     * @param array $data
     * @return array
     * @throws BusinessException
     */
    public function inputFilter(array $data): array
    {
        $allow_column = self::Db()->fetchAll("desc `".$this->table()."`");
        if (!$allow_column) {
            throw new \Exception('表不存在', 2); // 这里应该搞个错误类
        }
        $columns = array_column($allow_column, 'Type', 'Field');
        foreach ($data as $col => $item) {
            if (!isset($columns[$col])) {
                unset($data[$col]);
                continue;
            }
            // 非字符串类型传空则为null
            if ($item === '' && strpos(strtolower($columns[$col]), 'varchar') === false && strpos(strtolower($columns[$col]), 'text') === false) {
                $data[$col] = null;
            }
            if (is_array($item)) {
                $data[$col] = implode(',', $item);
            }
        }
        if (empty($data['created_at'])) {
            unset($data['created_at']);
        }
        if (empty($data['updated_at'])) {
            unset($data['updated_at']);
        }
        return $data;
    }
}
