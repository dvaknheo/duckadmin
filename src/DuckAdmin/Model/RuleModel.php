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
		$sql= "select * from wa_rules  order by weight desc";
		$data = static::Db()->fetchAll($sql);
		return $data;
	}
	public function checkWildRules($rule_ids,$controller,$action)
	{
		$str = static::Db()->qouteIn($ruls_ids);
		$key = static::Db()->qoute($controller);
		$like = static::Db()->qoute($controller.'@%');
		
		$sql = "select * from wa_rules where id in $str and (key = $key  or key like $like)";
		$data = static::Db()->fetchColumn($sql);
		return $data;
	}
	public function checkRules($rule_ids,$controller,$action)
	{
		$str = static::Db()->qouteIn($ruls_ids);
		$full = static::Db()->qoute($controller.'@'.$action);
		$c = static::Db()->qoute($controller);
		
		$sql = "select * from wa_rules where id in $str and (key = $full  or key = $c)";
		$data = static::Db()->fetchColumn($sql);
		return $data;
	}
    public function dropByIds($delete_ids)
	{
		$sql= "delete from wa_rules where in " . static::Db()->quoteIn($delete_ids);
		return static::Db()->exec($sql);
	}
	////////////////////////////////////////////
	protected function updateMenu($key,$menu)
	{
		$pid = $menu['pid']??0;
		$time = date('Y-m-d H:i:s');
		$sql = "update wa_rules set pid=?, title=?, icon=?, updated_at=? where `key`=?";
		return static::Db()->execute($sql, $pid, $menu['title'], $menu['icon']??null, $time, $key);
	}
	protected function addMenu($key,$menu)
	{
		$pid = $menu['pid']??0;
		$time = date('Y-m-d H:i:s');
		$sql = "insert into wa_rules (pid,title,icon,`key`,created_at,updated_at) values(?,?,?,?,?,?)";
		static::Db()->execute($sql,$pid,$menu['title'], $menu['icon']??null, $key,$time,$time);
		return static::Db()->lastInsertId();
	}
	public function findByKey($key)
	{
		$sql="select * from wa_rules where `key`= ?";
		return static::Db()->fetch($sql, $key);
	}
	protected function get_children_ids($ids)
	{
		$sql ="select id from wa_rules where pid in " .static::Db()->qouteIn($ids);
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
