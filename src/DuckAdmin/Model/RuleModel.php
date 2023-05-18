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
	public function isSuper($rules)
	{
		return $rules && in_array('*', $rules);
	}
	public function allRules()
	{
		$sql= "select * from wa_rule  order by weight desc";
		$data = static::Db()->fetchAll($sql);
		return $data;
	}
	public function checkWildRules($rule_ids,$controller,$action)
	{
		$str = static::Db()->qouteIn($ruls_ids);
		$key = static::Db()->qoute($controller);
		$like = static::Db()->qoute($controller.'@%');
		
		$sql = "select * from wa_rule where id in $str and (key = $key  or key like $like)";
		$data = static::Db()->fetchColumn($sql);
		return $data;
	}
	public function checkRules($rule_ids,$controller,$action)
	{
		$str = static::Db()->qouteIn($ruls_ids);
		$full = static::Db()->qoute($controller.'@'.$action);
		$c = static::Db()->qoute($controller);
		
		$sql = "select * from wa_rule where id in $str and (key = $full  or key = $c)";
		$data = static::Db()->fetchColumn($sql);
		return $data;
	}
    public function dropByIds($delete_ids)
	{
		$sql= "delete from wa_rule where in " . static::Db()->quoteIn($delete_ids);
		return static::Db()->exec($sql);
	}
	////////////////////////////////////////////
	public function updateMenuTree($key,$menu_tree)
	{
		Rule::where('key', $menu_tree['key'])->update($menu_tree);  // 这里 要从 安装类里找
	}
	public function findByKey($key)
	{
		$sql="select * from wa_rule where key = ?";
		return static::Db()->fetch($sql, $key);
	}
	public function get_children_ids($ids)
	{
		$children_ids = Rule::whereIn('pid', $ids)->pluck('id')->toArray();
		
	}
    /**
     * 删除菜单
     * @param $key
     * @return void
     */
    public static function deleteAll($key)
    {
        $item = RuleModel::G()->findByKey($key);
        if (!$item) {
            return;
        }
        // 子规则一起删除
        $delete_ids = $children_ids = [$item['id']];
        while($children_ids) {
            $children_ids = RuleModel::G()->get_children_ids($children_ids);
            $delete_ids = array_merge($delete_ids, $children_ids);
        }
		RuleModel::dropByIds($delete_ids);
    }

	////////////
	    /**
     * 导入菜单
     * @param array $menu_tree
     * @return void
     */
    public static function import(array $menu_tree)
    {
        if (is_numeric(key($menu_tree)) && !isset($menu_tree['key'])) {
            foreach ($menu_tree as $item) {
                static::import($item);
            }
            return;
        }
        $children = $menu_tree['children'] ?? [];
        unset($menu_tree['children']);
        if ($old_menu = static::get($menu_tree['key'])) {
            $pid = $old_menu['id'];
            RuleModel::G()->updateMenuTree($menu_tree['key'],$menu_tree);
        } else {
            $pid = static::add($menu_tree);
        }
        foreach ($children as $menu) {
            $menu['pid'] = $pid;
            static::import($menu);
        }
    }
	////////////////
	///////////////////////
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
