<?php declare(strict_types=1);
/**
 * DuckPhp
 * From this time, you never be alone~
 */

namespace DuckAdmin\Business;

use DuckAdmin\System\ProjectBusiness;
use DuckPhp\Core\App;

use DuckAdmin\Model\AdminRoleModel;
use DuckAdmin\Model\RoleModel;
use DuckAdmin\Model\RuleModel;

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
    public function selectInput($data, $table, $dataLimit=null, $dataLimitField =null): array
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
	///////////////////////////////////////////////
	/**
     * 插入前置方法
     * @param Request $request
     * @return array
     * @throws BusinessException
     */
    protected function insertInput($input): array
    {
		//这个要移动出去
        $table = config('plugin.admin.database.connections.mysql.prefix') . $this->model->getTable();
        $allow_column = $this->model->getConnection()->select("desc `$table`");
        if (!$allow_column) {
            throw new BusinessException('表不存在', 2);
        }
        $columns = array_column($allow_column, 'Type', 'Field');
		
        $data = $this->inputFilter($input,$columns);
		

        if (!Auth::isSupperAdmin() && $this->dataLimit) {
            if (!empty($data[$this->dataLimitField])) {
                $admin_id = $data[$this->dataLimitField];
                if (!in_array($admin_id, Auth::getScopeAdminIds(true))) {
                    throw new BusinessException('无数据权限');
                }
            }
        }
        return $data;
    }
    /**
     * 对用户输入表单过滤
     * @param array $data
     * @return array
     * @throws BusinessException
     */
    protected function inputFilter(array $data,$columns): array
    {

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
	///////////////////////
	protected function noRole($admin_id,$role_id,bool $with_self = false)
	{
		$roles = AdminRoleModel::G()->getRoleIds($admin_id);
		if(!$this->isSupperAdmin($admin_id) && !in_array($role_id, $this->getScopeRoleIds($roles, $with_self))){
			return true;
		}else{
			return false;
		}
	}
	public function isSupperAdmin(int $admin_id = 0): bool
    {
		static::ThrowOn($admin_id==0,'参数错误，请指定管理员');
		$roles = AdminRoleModel::G()->getRoles($admin_id);
        $rules = RoleModel::G()->getRules($roles);
        return RuleModel::G()->isSuper($rules); 
    }
	/**
     * 获取权限范围内的所有角色id
     * @param bool $with_self
     * @return array
     */
    public function getScopeRoleIds($role_ids,  bool $with_self = false): array
    {
        $role_ids = $admin['roles'];
		
        $rules = RoleModel::G()->getRules($role_ids);
        if ($rules && in_array('*', $rules)) {
            return RoleModel::G()->getAllId();
        }
        $roles = RoleModel::G()->getAll();
		
        $tree = new Tree($roles);
        $descendants = $tree->getDescendant($role_ids, $with_self);
        return array_column($descendants, 'id');
    }
}
