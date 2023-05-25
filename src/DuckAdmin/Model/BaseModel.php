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
    
    public static function GetTableByClass($class)
    {
        return static::G()->_GetTableByClass($class);
    }
    public function _GetTableByClass($class)
    {
        // 表前缀要跟着自己，而不是系统
        $this->table_prefix = $table_prefix ?? (\DuckAdmin\System\App::G()->options['table_prefix']??'');
        $t = explode('\\', $class);
        $class = array_pop($t);
        
        $table_name = 'admin_'.strtolower(substr($class,0,-5));
        
        return $table_name;
    }

    public function table()
    {
        if(!isset($this->table_name)){
            $this->table_name = self::GetTableByClass(static::class);
        }
        return $this->table_name;
    }
	
	//这对外
	public function doSelect(array $where, string $field = null, string $order= 'desc' ,$page=1,$page_size=10)
    {
		$sql_where =' TRUE ';
        foreach ($where as $column => $value) {
            if (is_array($value)) {
                if (in_array($value[0], ['>', '=', '<', '<>', 'like', 'not like'])) {
					$sql_where.=" and`$column` ". $value[0] . self::Db()->qoute($value[1]);
                } elseif ($value[0] == 'in') {
					$sql_where.="`$column` in(". self::Db()->qouteIn($value[1]).")";
                } elseif ($value[0] == 'not in') {
					$sql_where.="`$column` not in(". self::Db()->qouteIn($value[1]).")";
                } elseif ($value[0] == 'null') {
					$sql_where.="`$column`  is_null()";
                } elseif ($value[0] == 'not null') {
					$sql_where.="`$column`  not null()";
                } else {
					$sql_where.="`$column`  between(".self::Db()->qouteIn($value[1]).")";
                }
            } else {
				$sql_where.="`$column` = ". self::Db()->qoute($value);
            }
        }
		// 我们老土的用 sql  语句来完成代码
		$sql = "select * from 'TABLE' where $sql_where";
		$total = self::Db()->table($this->table())->fetchColumn(self::SqlForCountSimply($sql));
		if ($field) {
			$sql .=" order by `$field` $order "; // 这里可能会有些问题
		}
		$items = self::Db()->table($this->table())->fetchAll(self::SqlForPager($sql, $page, $page_size));
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

    /**
     * 执行插入
     * @param array $data
     * @return mixed|null
     */
    protected function doInsert(array $data)
    {
        $primary_key = $this->model->getKeyName();
        $model_class = get_class($this->model);
        $model = new $model_class;
        foreach ($data as $key => $val) {
            $model->{$key} = $val;
        }
        $model->save();
        return $primary_key ? $model->$primary_key : null;
    }
    /**
     * 执行更新
     * @param $id
     * @param $data
     * @return void
     * @throws BusinessException
     */
    protected function doUpdate($id, $data)
    {
        $model = $this->model->find($id);
        if (!$model) {
            throw new BusinessException('记录不存在', 2);
        }
        foreach ($data as $key => $val) {
            $model->{$key} = $val;
        }
        $model->save();
    }

    
	/////////////////////// 以下代码没用上///////////////////////////
    public function getList(int $page = 1, int $page_size = 10)
    {
        $sql = "SELECT * from 'TABLE' where true order by id desc";
        $sql = $this->prepare($sql);
        
        $total = self::Db()->fetchColumn(self::SqlForCountSimply($sql));
        $data = self::Db()->fetchAll(self::SqlForPager($sql, $page, $page_size));
        return ['data'=>$data,"total"=>$total];
    }
    public function get($id)
    {
        $sql = "select * from 'TABLE' where id =?";
        $sql = $this->prepare($sql);
        $ret = self::Db()->fetch($sql, $id);
        return $ret;
    }
    public function find($a)
    {
        $f=[];
        foreach($a as $k => $v){
            $f[]= $k . ' = ' . self::Db()->quote($v);
        }
        $frag=implode('and ',$f);
        
        $sql = "select * from 'TABLE' where ".$frag;
        $sql = $this->prepare($sql);
        $ret = self::Db()->fetch($sql, $id);
        return $ret;
    }
    public function add($data)
    {
        $ret = self::Db()->insertData($this->table(), $data);
        return $ret;
    }
    public function update($id, $data)
    {
        $ret = self::Db()->updateData($this->table(), $id, $data);
        
        return $ret;
    }
    public function delete($id)
    {
        $date = date('Y-m-d H:i:s');
        $sql = "update 'TABLE' set deleted_at=? where id=? ";
        $sql = $this->prepare($sql);
        $ret = self::Db()->execute($sql, $date, $id);
        return $ret;
    }
	
}
