<?php
namespace DuckAdmin\Business;

use DuckAdmin\Model\RuleModel;
use DuckAdmin\Model\RoleModel;

/**
 * 个人资料业务
 */
class RuleBusiness extends BaseBusiness 
{
	public function getRules($roles)
	{
		$rules_strings = RoleModel::G()->getRules($roles);
		$rules = [];
        foreach ($rules_strings as $rule_string) {
            if (!$rule_string) {
                continue;
            }
            $rules = array_merge($rules, explode(',', $rule_string));
        }
        return $rules;
	}
	public function get($roles,$types)
	{
		$rules = RoleModel::G()->getRules($roles);
        $items = RuleModel::G()->allRules();
		
		// 格式化数据
        $formatted_items = [];
        foreach ($items as $item) {
            $item['pid'] = (int)$item['pid'];
            $item['name'] = $item['title'];
            $item['value'] = $item['id'];
            $item['icon'] = $item['icon'] ? "layui-icon {$item['icon']}" : '';
            $formatted_items[] = $item;
        }

        $tree = new Tree($formatted_items);
        $tree_items = $tree->getTree();
        // 超级管理员权限为 *
        if (!in_array('*', $rules)) {
            $this->removeNotContain($tree_items, 'id', $rules);
        }
        $this->removeNotContain($tree_items, 'type', $types);
		
        return Tree::arrayValues($tree_items);
	}
///////////////////////////////////
    /**
     * 移除不包含某些数据的数组
     * @param $array
     * @param $key
     * @param $values
     * @return void
     */
    protected function removeNotContain(&$array, $key, $values)
    {
        foreach ($array as $k => &$item) {
            if (!is_array($item)) {
                continue;
            }
            if (!$this->arrayContain($item, $key, $values)) {
                unset($array[$k]);
            } else {
                if (!isset($item['children'])) {
                    continue;
                }
                $this->removeNotContain($item['children'], $key, $values);
            }
        }
    }

    /**
     * 判断数组是否包含某些数据
     * @param $array
     * @param $key
     * @param $values
     * @return bool
     */
    protected function arrayContain(&$array, $key, $values): bool
    {
        if (!is_array($array)) {
            return false;
        }
        if (isset($array[$key]) && in_array($array[$key], $values)) {
            return true;
        }
        if (!isset($array['children'])) {
            return false;
        }
        foreach ($array['children'] as $item) {
            if ($this->arrayContain($item, $key, $values)) {
                return true;
            }
        }
        return false;
    }
	/////////////////////////////
	
	public function selectRules($data)
	{
		//$this->syncRules(); //暂时不同步
		[$where, $format, $limit, $field, $order] = $this->selectInput($data,RuleModel::G()->table());
        [$data,$total] = RuleModel::G()->doSelect($where, $field, $order);
        return $this->doFormat($data, $total, $format, $limit);
		
	}
	/**
     * 根据类同步规则到数据库
     * @return void
     */
    protected function syncRules()
    {
		static::ThrowOn(true,'未完成');
        $items = Rule::where('key', 'like', '%\\\\%')->get()->keyBy('key');
        $methods_in_db = [];
        $methods_in_files = [];
        foreach ($items as $item) {
            $class = $item->key;
            if (strpos($class, '@')) {
                $methods_in_db[$class] = $class;
                continue;
            }
            if (!class_exists($class)) {
				continue;
			}
			$reflection = new \ReflectionClass($class);
			$properties = $reflection->getDefaultProperties();
			$no_need_auth = array_merge($properties['noNeedLogin'] ?? [], $properties['noNeedAuth'] ?? []);
			$class = $reflection->getName();
			$pid = $item->id;
			$methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
			foreach ($methods as $method) {
				$method_name = $method->getName();
				if (strtolower($method_name) === 'index' || strpos($method_name, '__') === 0 || in_array($method_name, $no_need_auth)) {
					continue;
				}
				$name = "$class@$method_name";

				$methods_in_files[$name] = $name;
				$title = Util::getCommentFirstLine($method->getDocComment()) ?: $method_name;
				$menu = $items[$name] ?? [];
				if ($menu) {
					if ($menu->title != $title) {
						Rule::where('key', $name)->update(['title' => $title]);
					}
					continue;
				}
				$menu = new Rule;
				$menu->pid = $pid;
				$menu->key = $name;
				$menu->title = $title;
				$menu->type = 2;
				$menu->save();
			}
            
        }
        // 从数据库中删除已经不存在的方法
        $menu_names_to_del = array_diff($methods_in_db, $methods_in_files);
        if ($menu_names_to_del) {
            //RuleModel::whereIn('key', $menu_names_to_del)->delete();
        }
    }
	
	///////////////////////////////
	public function permission($roles)
	{
		$rules = $this->getRules($roles);
        if (in_array('*', $rules)) {
			return ['*'];
        }
		
        $keys = Rule::whereIn('id', $rules)->pluck('key');
        $permissions = [];
        foreach ($keys as $key) {
            if (!$key = Util::controllerToUrlPath($key)) {
                continue;
            }
            $code = str_replace('/', '.', trim($key, '/'));
            $permissions[] = $code;
        }
		return $permissions;
	}
	
	public function insertRule()
	{
        $data = $this->insertInput($request);
        if (empty($data['type'])) {
            $data['type'] = strpos($data['key'], '\\') ? 1 : 0;
        }
        $data['key'] = str_replace('\\\\', '\\', $data['key']);
        $key = $data['key'] ?? '';
        if (Rule::where('key', $key)->first()) {
			static::ThrowOn(true, "菜单标识 $key 已经存在", 1);
        }
        $data['pid'] = empty($data['pid']) ? 0 : $data['pid'];
        $this->doInsert($data);
	}
	public function updateRule()
	{
        [$id, $data] = $this->updateInput($request);
		$row = Rule::find($id);
		static::ThrowOn(!$row, '记录不存在',2);
        if (isset($data['pid'])) {
            $data['pid'] = $data['pid'] ?: 0;
            static::ThrowOn($data['pid'] == $row['id'], '不能将自己设置为上级菜单',2);
            
        }
        if (isset($data['key'])) {
            $data['key'] = str_replace('\\\\', '\\', $data['key']);
        }
        $this->doUpdate($id, $data);
	}
	public function deleteRule()
	{
		$primary_key = Rule::getKeyName();
        $ids = (array)$request->post($primary_key, []);
        if (!Auth::isSupperAdmin() && $this->dataLimit) {
            $admin_ids = Rule::where($primary_key, $ids)->pluck($this->dataLimitField)->toArray();
            if (array_diff($admin_ids, Auth::getScopeAdminIds(true))) {
                throw new BusinessException('无数据权限');
            }
        }

		// 子规则一起删除
        $delete_ids = $children_ids = $ids;
        while($children_ids) {
            $children_ids = Rule::whereIn('pid', $children_ids)->pluck('id')->toArray();
            $delete_ids = array_merge($delete_ids, $children_ids);
        }
        $this->doDelete($delete_ids);
	}
}