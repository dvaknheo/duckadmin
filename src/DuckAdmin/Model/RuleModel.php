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
	public function foo()
	{
		//这里有个 like
		$rule = Rule::where(function ($query) use ($controller, $action) {
			$controller_like = str_replace('\\', '\\\\', $controller);
			$query->where('key', 'like', "$controller_like@%")->orWhere('key', $controller);
		})->whereIn('id', $rule_ids)->first();
		return $rule;
		
		$sql= "select * from wa_rule where key  = ?  or  key ";
		$data = static::Db()->fetchAll($sql);
	}
	public function foo2()
	{
	        $rule = Rule::where(function ($query) use ($controller, $action) {
            $query->where('key', "$controller@$action")->orWhere('key', $controller);
        })->whereIn('id', $rule_ids)->first();
		return $rule;
	}
    public function dropByIds($delete_ids)
	{
		$sql= "delete from wa_rule where in " . static::Db()->quoteIn($delete_ids);
		return static::Db()->exec($sql);
	}
}