<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Model;
/**
 * 菜单模型
 */
class RuleModel extends BaseModel
{
	public $table_name = 'wa_rules';

	public function isSuper($rules)
	{
		return $rules && in_array('*', $rules);
	}
	public function allRules()
	{
		$sql= "select * from wa_rules order by weight desc";
		$data = static::Db()->fetchAll($sql);
		return $data;
	}
	public function checkWildRules($rule_ids,$controller,$action)
	{
		$str = static::Db()->quoteIn($ruls_ids);
		$key = static::Db()->quote($controller);
		$like = static::Db()->quote($controller.'@%');
		
		$sql = "select * from wa_rules where id in $str and (key = $key  or key like $like)";
		$data = static::Db()->fetchColumn($sql);
		return $data;
	}
	public function checkRules($rule_ids,$controller,$action)
	{
		$str = static::Db()->quoteIn($ruls_ids);
		$full = static::Db()->quote($controller.'@'.$action);
		$c = static::Db()->quote($controller);
		
		$sql = "select * from wa_rules where id in $str and (key = $full  or key = $c)";
		$data = static::Db()->fetchColumn($sql);
		return $data;
	}
	public function allRulesForTree()
	{
		$sql= "select * from wa_rules";
		$rules = static::Db()->fetchAll($sql);
		
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
		$sql = "select * from wa_rules where id =?";
		return static::Db()->fetch($sql,$id);
	}
	public function findByKey($key)
	{
		$sql = "select * from wa_rules where `key` =?";
		return static::Db()->fetch($sql,$key);
	}
	///////////////
    public function dropWithChildren($ids)
	{
        $delete_ids = $children_ids = $ids;
        while($children_ids) {
            $children_ids = RuleModel::G()->get_children_ids($children_ids);
            $delete_ids = array_merge($delete_ids, $children_ids);
        }
		// 这两个要合并
		$sql = "delete from wa_rules where id in (" . static::Db()->quoteIn($delete_ids).')';
		static::Db()->execute($sql);
	}
	////////////////////////////////////////////
	
	public function updateTitleByKey($name,$title)
	{
		$time = date('Y-m-d H:i:s');
		$sql = "update wa_rules set title=?, updated_at=? where `key`=?";
		return static::Db()->execute($sql, $title, $time, $key);
	}
	public function updateRule($id, $data)
	{
		if(isset($menu['key'])){
			$menu['`key`']=$menu['key'];  //修复 db 类的 bug
			unset($menu['key']);
		}
		
		$time = date('Y-m-d H:i:s');
		$data['updated_at'] =$time;
		return static::Db()->updateData("wa_rules", $id, $data, 'id');
	}
	protected function updateMenu($key,$menu)
	{
		$pid = $menu['pid']??0;
		$time = date('Y-m-d H:i:s');
		$sql = "update wa_rules set pid=?, title=?, icon=?, updated_at=? where `key`=?";
		return static::Db()->execute($sql, $pid, $menu['title'], $menu['icon']??null, $time, $key);
	}
	protected function addMenu($key,$menu)
	{
		$menu['`key`']=$menu['key'];  //修复 db 类的 bug
		unset($menu['key']);
		
		$time = date('Y-m-d H:i:s');
		$menu['created_at']=$time;
		$menu['updated_at']=$time;
		static::Db()->insertData('wa_rules',$menu);
		
		return static::Db()->lastInsertId();
	}

	public function get_children_ids($ids)
	{
		//这个函数名字要改
		$sql ="select id from wa_rules where pid in (" .static::Db()->quoteIn($ids) .")";
		$data = static::Db()->fetchAll($sql);
		return array_column($data,'id');
	}
    /**
     * 删除菜单
     * @param $key
     * @return void
     */
    public static function deleteAll($key)
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
		$sql ="select `key` from wa_rules where id in (" .static::Db()->quoteIn($rules) .")";
		$data = static::Db()->fetchAll($sql);
		return array_column($data,'key');
	}
	public function checkRulesExist($rule_ids)
	{
		$sql ="select count(*) as c where id in (" .static::Db()->quoteIn($rule_ids) .")";
		$data = static::Db()->fetchColumn($sql);
		return (count($rule_exists) === count($rule_ids))?true:false;
	}
	public function getAllByKey()
	{
		$sql ="select * from `wa_rules` where `key` like '%\\\\\\\\%'"; // 这要8个\ 猜猜看为什么
		$data = static::Db()->fetchAll($sql);
		$ret=[];
		foreach($data as $v){
			$ret[$v['key']]=$v;
		}
		return $ret;
	}	
	///////////////////////
	public function descTable()
	{
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
