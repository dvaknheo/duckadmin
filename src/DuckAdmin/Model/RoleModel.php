<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Model;
/**
 * 菜单模型
 */
class RoleModel extends BaseModel
{
	public function getRules2($roles)
	{
		$sql="select rules from wa_role where id in " . static::Db()->quoteIn($roles);
		$data = static::Db()->fetchColumn($sql);
		return $data;

	}
	public function getRules($roles)
	{
		Role::whereIn('id', $roles)->pluck('rules');
	}
	public function hasSuperAdmin($roles)
    {
		$sql="select rules from wa_role where id in " . static::Db()->quoteIn($roles);
		$rules = static::Db()->fetchColumn($sql);
		
        $rule_ids = [];
        foreach ($rules as $rule_string) {
            if (!$rule_string) {
                continue;
            }
            $rule_ids = array_merge($rule_ids, explode(',', $rule_string));
        }
		return $rule_ids;
	}

}