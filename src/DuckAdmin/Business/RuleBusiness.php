<?php
namespace DuckAdmin\Business;

use DuckAdmin\Model\AdminRoleModel;
use DuckAdmin\Model\RuleModel;
use DuckAdmin\Model\RoleModel;

/**
 * 菜单相关
 */
class RuleBusiness extends Base
{
    
    public function get($admin_id, $types)
    {
        $roles = AdminRoleModel::_()->rolesByAdminId($admin_id);

        $roles = $roles ?? [];
        $rules = RoleModel::_()->getRules($roles);
        //$rules =explode(',',$rules);
        $items = RuleModel::_()->allRules();
        // 格式化数据
        $formatted_items = [];
        foreach ($items as $item) {
            $item['pid'] = (int)$item['pid'];
            $item['name'] = $item['title'];
            $item['value'] = $item['id'];
            $item['icon'] = $item['icon'] ? "layui-icon {$item['icon']}" : '';
            $item['href'] =(preg_match('/^(https?:\/)?\//', $item['href']))? $item['href'] : __url($item['href']);
            unset($item['created_at']);
            unset($item['updated_at']);
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
                //can not find case 
                continue; //@codeCoverageIgnore
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
            // can not find case
            return false; //@codeCoverageIgnore
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
    
    public function selectRules($op_id, $input)
    {
        $this->syncRules(); //同步数据
        
        // 还要根据 role 来限制权限
        [$where, $format, $limit, $field, $order] = RuleModel::_()->selectInput($input);
        
        [$data,$total] = RuleModel::_()->doSelect($where, $field, $order,1,$limit);
        return CommonService::_()->doFormat($data, $total, $format, $limit);
        
    }

    /**
     * 根据类同步规则到数据库
     * @return void
     */
    protected function syncRules()
    {
        $items = RuleModel::_()->getAllByKey();
        $methods_in_db = [];
        $methods_in_files = [];
        foreach ($items as $item) {
            $class = $item['key'];
            //如果有@在内，那就继续
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
            $pid = $item['id'];
            $methods = $reflection->getMethods();
            foreach ($methods as $method) {
                if ($method->isConstructor()){continue;}
                if ($method->isDestructor()){continue;}
                if ($method->isStatic()){continue;}
                if (!$method->isPublic()){continue;}
                $method_name = $method->getName();
                
                //strtolower($method_name) === 'index' || 
                if (in_array($method_name, $no_need_auth)) {
                    continue;
                }
                
                $name = "$class@$method_name";
                
                $methods_in_files[$name] = $name;
                $title = $this->getCommentFirstLine($method->getDocComment()) ?: $method_name;
                
                $menu = $items[$name] ?? [];
                if (!$menu) {
                    RuleModel::_()->addMenu(['pid'=>$pid,'key'=>$name,'title'=>$title,'type'=>2]);
                    continue;
                }
                if ($menu['title'] != $title) {
                    RuleModel::_()->updateTitleByKey($name,$title);
                }
            }
            
        }
        // 从数据库中删除已经不存在的方法
        $menu_names_to_del = array_diff($methods_in_db, $methods_in_files);
        if ($menu_names_to_del) {
            //RuleModel::whereIn('key', $menu_names_to_del)->delete();
        }
    }
    
    ///////////////////////////////
    public function permission($admin_id)
    {
        $roles = AdminRoleModel::_()->rolesByAdminId($admin_id); 
        $roles = $roles ?? [];
        $rules_strings = RoleModel::_()->getRules($roles);
        $rules = [];
        foreach ($rules_strings as $rule_string) {
            if (!$rule_string) {
                continue;
            }
            $rules = array_merge($rules, explode(',', $rule_string));
        }
        if (in_array('*', $rules)) {
            return ['*'];
        }
        
        $keys = RuleModel::_()->getKeysByIds($rules);
        $permissions = [];
        foreach ($keys as $key) {
            if (!$key = $this->controllerToUrlPath($key)) {
                continue; // @codeCoverageIgnore
            }
            $code = str_replace('/', '.', trim($key, '/'));
            $permissions[] = 'app.admin.'.$code;
        }
        return $permissions;
    }
    public function insertRule($op_id, $input)
    {
        $data = RuleModel::_()->inputFilter($input);
        
        if (empty($data['type'])) {
            $data['type'] = strpos($data['key'], '\\') ? 1 : 0;
        }
        $data['key'] = str_replace('\\\\', '\\', $data['key']);
        $data['pid'] = empty($data['pid']) ? 0 : $data['pid'];
        $key = $data['key'] ?? '';
        $flag = RuleModel::_()->findByKey($key);
        Helper::BusinessThrowOn($flag, "菜单标识 $key 已经存在", 1);
        
        RuleModel::_()->addMenu($data);
    }
    public function updateRule($op_id, $input)
    {
        $id = $input['id'];
        $row = RuleModel::_()->findById($id);
        
        Helper::BusinessThrowOn(!$row, '记录不存在',2);
        $data = RuleModel::_()->inputFilter($input);
        //[$id, $data] = $this->updateInput($input);
        if (isset($data['pid'])) {
            $data['pid'] = $data['pid'] ?: 0;
            Helper::BusinessThrowOn($data['pid'] == $row['id'], '不能将自己设置为上级菜单',2);
        }
        if (isset($data['key'])) {
            $data['key'] = str_replace('\\\\', '\\', $data['key']);
        }
        
        RuleModel::_()->updateRule($id, $data);
    }
    public function deleteRule($op_id, $ids)
    {
        if(empty($ids)){return;}
        RuleModel::_()->dropWithChildren($ids);
    }
    
    /**
     * 类转换为url path
     * @param $controller_class
     * @return false|string
     */
    public function controllerToUrlPath($controller_class)
    {
        $key = strtolower($controller_class);
        $action = '';
        if (strpos($key, '@')) {
            [$key, $action] = explode( '@', $key, 2);
        }
        $prefix = 'plugin';
        $paths = explode('\\', $key);
        if (count($paths) < 2) {
            return false;
        }
        $base = '';
        if (strpos($key, "$prefix\\") === 0) {
            if (count($paths) < 4) {
                return false;
            }
            array_shift($paths);
            $plugin = array_shift($paths);
            $base = "/app/$plugin/";
        }
        array_shift($paths);
        foreach ($paths as $index => $path) {
            if ($path === 'controller') {
                unset($paths[$index]);
            }
        }
        $suffix = 'controller';
        $code = $base . implode('/', $paths);
        if (substr($code, -strlen($suffix)) === $suffix) {
            $code = substr($code, 0, -strlen($suffix));
        }
        return $action ? "$code/$action" : $code;
    }
    /**
     * 获取注释中第一行
     * @param $comment
     * @return false|mixed|string
     */
    public static function getCommentFirstLine($comment)
    {
        if ($comment === false) {
            return false;// @codeCoverageIgnore
        }
        foreach (explode("\n", $comment) as $str) {
            if ($s = trim($str, "*/\ \t\n\r\0\x0B")) {
                return $s;
            }
        }
        return $comment;// @codeCoverageIgnore
    }
}