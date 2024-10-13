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
            $item['href'] =(preg_match('/^(https?:\/)?\//', $item['href']??''))? $item['href'] : __url($item['href']);
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
        // 这里要改
        $methods_in_db = [];
        $methods_in_files = [];

        $data = FinderForAdminController::_()->getAllAdminMethod();
        
        foreach($data as $class =>$methods) {
            $reflection = new \ReflectionClass($class);
            $properties = $reflection->getDefaultProperties();
            $no_need_auth = array_merge($properties['noNeedLogin'] ?? [], $properties['noNeedAuth'] ?? []);
            
            $pid = 0;
            $parent_menu = RuleModel::_()->findByKey($class);
            if($parent_menu){
                $pid=$parent_menu['id'];
            }else{
                // 插入新的类权限
                $parent_menu = RuleModel::_()->findByKey('unknown');
                if(!$parent_menu){
                    RuleModel::_()->addMenu(['pid'=>0,'key'=>'unknown','title'=>'未分配权限','type'=>'0']);
                    $parent_menu = RuleModel::_()->findByKey('unknown');
                }
                $pid = RuleModel::_()->addMenu(['pid'=>$parent_menu['id'],'key'=>$class,'title'=>$class,'type'=>1]);
                $parent_menu = RuleModel::_()->findByKey($class);
                $pid=$parent_menu['id'];
            }
            
            foreach ($methods as $name => $url) {
                [$class,$method_name]=explode('@',$name);
                if (in_array($method_name, $no_need_auth)) {
                    continue;
                }
                $method = new \ReflectionMethod($class,$method_name);
                
                $title = $this->getCommentFirstLine($method->getDocComment()) ?: $method_name;
                $menu = RuleModel::_()->findByKey($name);
                if (!$menu) {
                    RuleModel::_()->addMenu(['pid'=>$pid,'key'=>$name,'title'=>$title,'type'=>2]);
                    continue;
                }
                if ($menu['title'] != $title) {
                    RuleModel::_()->updateTitleByKey($name,$title);
                }
            }
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