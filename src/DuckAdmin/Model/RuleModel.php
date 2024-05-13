<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Model;
/**
 * 菜单模型
 */
class RuleModel extends Base
{
	public $table_name = 'rules';
	
    public function selectInput($data): array
	{
		// 隔离BaseModel 的调用
		return parent::selectInput($data);
	}
	public function doSelect(array $where, string $field = null, string $order= 'desc' ,$page=1,$page_size=10)
	{
		// 隔离BaseModel 的调用
		return parent::doSelect($where, $field, $order,$page,$page_size);
	}
    public function inputFilter(array $data): array
	{
		// 隔离BaseModel 的调用
		return parent::inputFilter($data);
	}

	public function isSuper($rules)
	{
		return $rules && in_array('*', $rules);
	}
	public function allRules()
	{
		$sql = "select * from `'TABLE'` order by weight desc";
		$data = $this->fetchAll($sql);
		return $data;
	}
	public function checkWildRules($rule_ids,$controller,$action)
	{
		$str = static::Db()->quoteIn($rule_ids);
		$key = static::Db()->quote($controller);
		$like = static::Db()->quote($controller.'@%');
		
		$sql = "select * from `'TABLE'` where id in $str and (key = $key  or key like $like)";
		$data = $this->fetchColumn($sql);
		return $data;
	}
	public function checkRules($rule_ids,$controller,$action)
	{
        // 根据 key 来判断权限
		$str = static::Db()->quoteIn($ruls_ids);
		$full = static::Db()->quote($controller.'@'.$action);
		$controller = static::Db()->quote($controller);
		
		$sql = "select * from `'TABLE'` where id in $str and (key = $full  or key = $controller)";
		$data = $this->fetchColumn($sql);
		return $data;
	}
	public function allRulesForTree()
	{
		$sql= "select * from `'TABLE'`";
		$rules = $this->fetchAll($sql);
		
        $items = [];
        foreach ($rules as $item) {
            $items[] = [
                'name' => $item['title'] ?? $item['name'] ?? $item['id'],
                'value' => (string)$item['id'],
                'id' => $item['id'],
                'pid' => $item['pid'],
            ];
        }
		return $items;
	}
	public function findById($id)
	{
		$sql = "select * from `'TABLE'` where id =?";
		return $this->fetch($sql,$id);
	}
	public function findByKey($key)
	{
		$sql = "select * from `'TABLE'` where `key` =?";
		return $this->fetch($sql,$key);
	}
	///////////////
    public function dropWithChildren($ids)
	{
		$ids=is_array($ids)?$ids:[$ids];
        $delete_ids = $children_ids = $ids;
        while($children_ids) {
            $children_ids = $this->get_children_ids($children_ids);
            $delete_ids = array_merge($delete_ids, $children_ids);
        }
		// 这两个要合并
		$sql = "delete from `'TABLE'` where id in (" . static::Db()->quoteIn($delete_ids).')';
		$this->execute($sql);
	}
	////////////////////////////////////////////
	
	public function updateTitleByKey($name,$title)
	{
		$time = date('Y-m-d H:i:s');
		$sql = "update `'TABLE'` set title=?, updated_at=? where `key`=?";
		return $this->execute($sql, $title, $time, $key);
	}
	public function updateRule($id, $data)
	{
		$time = date('Y-m-d H:i:s');
		$data['updated_at'] =$time;
		$data['pid'] = $data['pid']? $data['pid']:0;
		return $this->update($id, $data, 'id');
	}
	protected function updateMenu($key,$menu)
	{
		$pid = $menu['pid']??0;
		$time = date('Y-m-d H:i:s');
		$sql = "update `'TABLE'` set pid=?, title=?, icon=?, updated_at=? where `key`=?";
		return $this->execute($sql, $pid, $menu['title'], $menu['icon']??null, $time, $key);
	}
	public function addMenu($key,$menu)
	{
		$time = date('Y-m-d H:i:s');
		$menu['created_at']=$time;
		$menu['updated_at']=$time;
		$this->add($menu);
		
		return static::Db()->lastInsertId();
	}

	protected function get_children_ids($ids)
	{
		//这个函数名字要改
		$sql ="select id from `'TABLE'` where pid in (" .static::Db()->quoteIn($ids) .")";
		$data = $this->fetchAll($sql);
		return array_column($data,'id');
	}
    /**
     * 删除菜单
     * @param $key
     * @return void
     */
    public function deleteAll($key)
    {
        $item = $this->findByKey($key);
        if (!$item) {
            return;
        }
        // 子规则一起删除
        $delete_ids = $children_ids = [$item['id']];
        while($children_ids) {
            $children_ids = $this->get_children_ids($children_ids);
            $delete_ids = array_merge($delete_ids, $children_ids);
        }
		$this->dropByIds($delete_ids);
    }
	/**
     * 导入菜单
     * @param array $menu_tree
     * @return void
     */
    public function importMenu(array $menu_tree)
    {
        if (is_numeric(key($menu_tree)) && !isset($menu_tree['key'])) {
            foreach ($menu_tree as $item) {
                $this->importMenu($item);
            }
            return;
        }
        $children = $menu_tree['children'] ?? [];
        unset($menu_tree['children']);
        if ($old_menu = $this->findByKey($menu_tree['key'])) {
            $pid = $old_menu['id'];
            $this->updateMenu($menu_tree['key'],$menu_tree);
        } else {
            $pid = $this->addMenu($menu_tree['key'],$menu_tree);
        }
        foreach ($children as $menu) {
            $menu['pid'] = $pid;
            $this->importMenu($menu);
        }
    }

	public function getKeysByIds($rules)
	{
		$sql ="select `key` from `'TABLE'` where id in (" .static::Db()->quoteIn($rules) .")";
		$data = $this->fetchAll($sql);
		return array_column($data,'key');
	}
	public function checkRulesExist($rule_ids)
	{
		$sql ="select count(*) as c from `'TABLE'`  where id in (" .static::Db()->quoteIn($rule_ids) .")";
		$data = $this->fetchColumn($sql);
		$data = (int)$data;
		return ($data === count($rule_ids))?true:false;
	}
	public function getAllByKey()
	{
		$sql ="select * from `'TABLE'` where `key` like '%\\\\\\\\%'"; // 这要8个\ 猜猜看为什么
		$data = $this->fetchAll($sql);
		$ret=[];
		foreach($data as $v){
			$ret[$v['key']]=$v;
		}
		return $ret;
	}	
	///////////////////////
	public function descTable()
	{
        throw new \Exception('TODO: compat with sqlite');
		$sql ="desc `wa_rules`";
		$data = static::Db()->fetchAll($sql);
        $columns = array_column($data, 'Type', 'Field');
		return $columns;
	}
	////////////////
    /**
     * 获取菜单中某个(些)字段的值
     * @param $menu
     * @param null $column
     * @param null $index
     * @return array|mixed
     */
    public static function column($menu, $column = null, $index = null)
    {
        $values = [];
        if (is_numeric(key($menu)) && !isset($menu['key'])) {
            foreach ($menu as $item) {
                $values = array_merge($values, static::column($item, $column, $index));
            }
            return $values;
        }

        $children = $menu['children'] ?? [];
        unset($menu['children']);
        if ($column === null) {
            if ($index) {
                $values[$menu[$index]] = $menu;
            } else {
                $values[] = $menu;
            }
        } else {
            if (is_array($column)) {
                $item = [];
                foreach ($column as $f) {
                    $item[$f] = $menu[$f] ?? null;
                }
                if ($index) {
                    $values[$menu[$index]] = $item;
                } else {
                    $values[] = $item;
                }
            } else {
                $value = $menu[$column] ?? null;
                if ($index) {
                    $values[$menu[$index]] = $value;
                } else {
                    $values[] = $value;
                }
            }
        }
        foreach ($children as $child) {
            $values = array_merge($values, static::column($child, $column, $index));
        }
        return $values;
    }
	

}
