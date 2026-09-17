<?php declare(strict_types=1);
/**
 * DuckPhp Admin System - Admin Tree Builder
 */
namespace DuckAdmin\Admin\Business;

use DuckPhp\Component\RouteLister;
use DuckPhp\Core\App;
use DuckPhp\Core\ComponentBase;
use DuckPhp\Core\Route;
// 我们要分为build 自己的，和 scanall 全局两种。
// build 用于生成
class AdminTreeBuilder extends ComponentBase
{
    public function buildAndSaveToConfigJsonFile()
    {
        $routes = $this->getRoutes(true);
        $tree = $this->build($routes);
        
        $menu_file = App::_()->options['permission_menu_tree_for_admin'] ?? null;
        if (!$menu_file) {
            return;
        }
        $filename = App::_()->getConfigFile($menu_file);
        $data = json_encode($tree, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        file_put_contents($filename, $data);
    }
    public function loadAdminPermissionMenu(bool $force_build = false)
    {
        $prefix = App::_()->options['controller_url_prefix'] ?? '';
        $prefix = '/' . $prefix;

        if ($force_build) {
            $routes = $this->getRoutes(false);
            $menuTree = $this->build($routes);
            return $menuTree;
        }

        $menu_file = App::_()->options['permission_menu_tree_for_admin'] ?? null;
        if ($menu_file) {
            $filename = App::_()->getConfigFile($menu_file);
            if ('.json' === substr($filename, -strlen('.json'))) {
                $menuTree = json_decode(file_get_contents($filename), true);
            } else {
                $menuTree = @include $filename;
                $menuTree = is_array($menuTree) ? $menuTree : [];
            }
            $menuTree = $this->resolveUrls($menuTree, $prefix);
        } else {
            $routes = $this->getRoutes(false);
            $menuTree = $this->build($routes);
        }

        return $menuTree;
    }
    public function loadAllAdminPermissionMenu(bool $force_build = false)
    {
        $current_phase = App::Phase();
        $tree = $this->loadAdminPermissionMenu($force_build);

        App::Root(true);
        $this->mergeAppsMenus($tree, $current_phase, $force_build);
        App::Phase($current_phase);

        return $tree;
    }
    /**
     * 递归合并子 app 的菜单（从 root 开始）
     *
     * @param array &$tree 合并到的树
     * @param string $ignore_phase 忽略的 phase（当前 phase）
     */
    protected function mergeAppsMenus(array &$tree, string $ignore_phase, bool $force_build): void
    {
        $app = App::_();
        $current_phase = App::Phase();
        $child_apps = $app->options['app'] ?? [];

        $item = $this->loadAdminPermissionMenu($force_build);
        $tree = array_merge($tree, $item);
        foreach ($child_apps as $class => $app_options) {
            $child_app = $app->toThisChild($class);
            if ($child_app === null) {
                continue;
            }
            $child_phase = App::Phase();
            if ($child_phase === $ignore_phase) {
                continue;
            }
            $this->mergeAppsMenus($tree, $ignore_phase, $force_build);
            App::Phase($current_phase);
        }
    }
    protected function getRoutes(bool $trim_url = false)
    {
        $routes = RouteLister::_()->listAll(false, true, true);
        if (!$trim_url) {
            return $routes;
        }
        // 转换为相对地址
        $prefix = App::_()->options['controller_url_prefix'] ?? '';
        foreach ($routes as &$route) {
            $route['url'] = substr($route['url'], strlen($prefix));
        }
        unset($route);
        return $routes;
    }
    ////////////////////////////////////////////////////////
    /**
     * 构建菜单树：RouteLister 扫描路由，按注解生成 分组→目录→菜单/操作 树形结构
     *
     * 支持两种模式：
     * 1. 注解模式：直接在 controller 类和方法上写注解
     * 2. __MenuMeta 模式：如果类提供了 public static function __MenuMeta() 返回菜单元数据
     *
     * 类层级注解：
     * - @menu_directory 名称 [url]   顶级分组，支持 \ 切分生成多级目录
     *                           url 可选：无则取首个方法的 dirname + '/#'，如 Admin/index → Admin/#
     * - @menu_icon 图标名称        目录图标
     * - @menu_weight N           本层权重，越大越靠前
     *
     * 方法层级注解：
     * - @menu_directory 名称 可选 顶级分组， 支持 \ 切分生成多级目录 ，插入相应的目录
     * - @menu_icon 图标名称     给菜单/操作节点设置图标
     * - @menu 名称               菜单（type=1），url 取路由完整 path
     * - @menu_action 名称        动作（type=2）
     * - @menu_permission #url 名称   权限项（type=3），#url 会被加上方法 url 前缀
     * - @menu_weight N           本层权重
     * - 公开方法无任何注解       视为 动作（type=2），名称为方法名
     *
     * 后处理：切分子层级、排序
     *
     * @return array[] 树形精简菜单结构
     */
    public function build(array $routes): array
    {
        // 1. 按 controller 分组
        $controllers = [];
        foreach ($routes as $route) {
            $controller = (string) ($route['controller'] ?? '');
            $method = (string) ($route['method'] ?? '');
            $url = (string) ($route['url'] ?? '');
            $controllers[$controller][$method] = $url;
        }

        // 2. 处理每个 controller
        $items = [];
        foreach ($controllers as $controller => $methods) {
            // 检查 __MenuMeta 静态方法
            $meta = $this->getClassMenuMeta($controller);
            if ($meta !== null) {
                $items = array_merge($items, $this->processMenuMetaForController($meta, $methods));
                continue;
            }

            // 注解模式
            $classDoc = $this->getClassDoc($controller);
            // class 层级的目录注解、图标、权重
            $dirAnno = $this->parseAnnotatedLine($classDoc, 'menu_directory');
            $dirIcon = $this->parseAnnotatedLine($classDoc, 'menu_icon');
            $dirWeight = $this->parseWeight($classDoc);

            // 处理这个 controller 下的所有方法，收集子节点
            $childItems = [];
            foreach ($methods as $method => $url) {
                $childItems = array_merge($childItems, $this->collectMethodItems($controller, $method, $url));
            }

            // 计算目录 url：取首个方法的 dirname + '/#'
            $dirUrl = '';
            if (!empty($methods)) {
                $firstUrl = reset($methods);
                $pos = strrpos($firstUrl, '/');
                if ($pos !== false) {
                    $dirUrl = substr($firstUrl, 0, $pos + 1) . '#';
                }
            }

            // 生成目录节点，子节点挂靠其下
            if (!empty($childItems)) {
                $items[] = [
                    'name' => $dirAnno ? $dirAnno[0] : 'NoName',
                    'url' => $dirUrl,
                    'icon' => $dirIcon ? $dirIcon[0] : null,
                    'type' => 0,
                    'weight' => $dirWeight,
                    'children' => $childItems,
                ];
            }
        }

        // 3. 切分子层级（处理 \ 分割的多级目录）
        $tree = $this->splitSubLevels($items);

        // 4. 排序（同级排序，weight 只在同级有效）
        $this->sortTree($tree);
        return $tree;
    }

    /**
     * 获取类的 __MenuMeta 静态方法返回的菜单元数据
     *
     * @param string $controller 控制器类名
     * @return array|null 如果存在返回数组，否则 null
     */
    protected function getClassMenuMeta(string $controller): ?array
    {
        if (!class_exists($controller)) {
            return null;
        }
        if (!method_exists($controller, '__MenuMeta')) {
            return null;
        }
        $ref = new \ReflectionMethod($controller, '__MenuMeta');
        if (!$ref->isStatic()) {
            return null;
        }
        try {
            return $controller::__MenuMeta() ?? null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * 处理 __MenuMeta 返回的数据（按 controller 下的所有 methods）
     *
     * @param array $meta __MenuMeta 返回的数组
     * @param array $methods controller 下的所有方法 [method => url]
     * @return array items 数组
     */
    protected function processMenuMetaForController(array $meta, array $methods): array
    {
        $items = [];
        foreach ($meta as $item) {
            $name = (string) ($item['name'] ?? '');
            $type = (int) ($item['type'] ?? 1);
            $url = ($item['url'] ?? null);
            $weight = (int) ($item['weight'] ?? 0);

            $items[] = [
                'name' => $name,
                'url' => $url,
                'type' => $type,
                'weight' => $weight,
            ];
        }
        return $items;
    }

    /**
     * 收集方法的注解信息，生成子节点
     *
     * @param string $controller 控制器类名
     * @param string $method 方法名
     * @param string $url 路由路径
     * @return array 子节点数组
     */
    protected function collectMethodItems(string $controller, string $method, string $url): array
    {
        $items = [];
        $mDoc = $this->getMethodDoc($controller, $method);
        $weight = $this->parseWeight($mDoc);

        // 方法的 @menu_directory 注解（如果有，记录下来用于 splitSubLevels）
        $methodDir = $this->parseAnnotatedLine($mDoc, 'menu_directory');
        // 方法的 @menu_icon 注解
        $methodIcon = $this->parseAnnotatedLine($mDoc, 'menu_icon');

        // @menu 优先，其次 @menu_action，默认 action
        $menuAnno = $this->parseAnnotatedLine($mDoc, 'menu');
        if ($menuAnno !== null) {
            $name = $menuAnno[0];
            $type = 1;
        } elseif ($actionAnno = $this->parseAnnotatedLine($mDoc, 'menu_action')) {
            $name = $actionAnno[0];
            $type = 2;
        } else {
            $name = $method;
            $type = 2;
            $weight = 0;
        }
        $items[] = [
            'name' => $name,
            'url' => $url,
            'type' => $type,
            'weight' => $weight,
            'directory' => $methodDir ? $methodDir[0] : '',
            'icon' => $methodIcon ? $methodIcon[0] : null,
        ];

        // @menu_permission #url Name（可能有多个，放最后）
        foreach ($this->parseMultiAnnotatedLine($mDoc, 'menu_permission') as $permAnno) {
            $permUrl = $permAnno[0] ?? '';
            $permName = $permAnno[1] ?? '';
            if ($permName === '') {
                continue;
            }
            if (strpos($permUrl, '#') === 0) {
                $permUrl = $url . $permUrl;
            }
            $items[] = [
                'name' => $permName,
                'url' => $permUrl,
                'type' => 3,
                'weight' => $weight,
                'directory' => $methodDir ? $methodDir[0] : '',
                'icon' => null,
            ];
        }

        return $items;
    }

    /**
     * 解析多行同类型注解（如多个 @menu_permission）
     *
     * @param string $doc docblock
     * @param string $tag 注解名称
     * @return array<int,array{0:string,1:string}> 二维数组，每行一个解析结果
     */
    protected function parseMultiAnnotatedLine(string $doc, string $tag): array
    {
        $results = [];
        if (!preg_match_all('/@' . $tag . '\s+([^*\n]+)/', $doc, $matches)) {
            return $results;
        }
        foreach ($matches[1] as $match) {
            $parts = preg_split('/\s+/', trim($match));
            $first = (string) array_shift($parts);
            $second = (string) ($parts[0] ?? '');
            $results[] = [$first, $second];
        }
        return $results;
    }

    /**
     * 切分子层级（处理 \ 分割的多级目录，以及子节点的 directory 信息）
     *
     * @param array $nodes 原始树节点
     * @return array 处理后的树
     */
    protected function splitSubLevels(array $nodes): array
    {
        $tree = [];

        foreach ($nodes as $node) {
            // 清理子节点的临时字段，并分离出带 directory 的子节点
            $normalChildren = [];
            $dirChildren = [];
            foreach ($node['children'] ?? [] as $child) {
                $dir = $child['directory'] ?? '';
                unset($child['directory'], $child['icon']);
                if ($dir !== '') {
                    $dirChildren[$dir][] = $child;
                } else {
                    $normalChildren[] = $child;
                }
            }
            $node['children'] = $normalChildren;

            // 处理节点 name 的 \ 分割
            $parts = explode('\\', $node['name']);
            $this->mergeNode($tree, $parts, $node);

            // 处理带 directory 的子节点
            foreach ($dirChildren as $dirName => $children) {
                $dirParts = explode('\\', $dirName);
                $dirNode = [
                    'name' => $dirParts[count($dirParts) - 1],
                    'url' => null,
                    'type' => 0,
                    'children' => $children,
                ];
                $this->mergeNode($tree, $dirParts, $dirNode);
            }
        }

        return $tree;
    }

    /**
     * 将节点合并到树中（按路径创建目录）
     *
     * @param array &$tree 目标树（引用）
     * @param array $parts 路径分割数组
     * @param array $node 要合并的节点
     */
    protected function mergeNode(array &$tree, array $parts, array $node): void
    {
        $name = array_shift($parts);
        $isLast = empty($parts);

        // 查找同名目录节点
        foreach ($tree as &$item) {
            if ($item['name'] === $name && $item['type'] === 0) {
                if ($isLast) {
                    $item['children'] = array_merge($item['children'], $node['children'] ?? []);
                } else {
                    $this->mergeNode($item['children'], $parts, $node);
                }
                return;
            }
        }
        unset($item);

        // 未找到，创建新节点
        if ($isLast) {
            $tree[] = $node;
        } else {
            $tree[] = [
                'name' => $name,
                'url' => null,
                'type' => 0,
                'children' => [],
            ];
            end($tree);
            $idx = key($tree);
            $this->mergeNode($tree[$idx]['children'], $parts, $node);
        }
    }
    /**
     * 递归排序树：同级按 weight 排序，weight 越大越靠前，输出时清理 weight 字段
     *
     * @param array &$nodes 树节点数组（引用传递）
     */
    protected function sortTree(array &$nodes): void
    {
        // 按 weight 降序排序（同级排序）
        uasort($nodes, function ($a, $b) {
            return ($b['weight'] ?? 0) <=> ($a['weight'] ?? 0);
        });

        // 重新索引为连续数组
        $nodes = array_values($nodes);

        // 递归排序 children
        foreach ($nodes as &$node) {
            unset($node['weight']);
            if (!empty($node['children'])) {
                $this->sortTree($node['children']);
            }
        }
        unset($node);
    }

    /**
     * 补全树中的相对 url 为绝对 url（加挂载前缀）
     * @param array &$tree 树形结构（引用传递，直接修改原树）
     * @param string $prefix 挂载前缀，如 /admin/
     * @return array 补全后的树
     */
    public function resolveUrls(array &$tree, string $prefix): array
    {
        $this->walkTree($tree, function (&$node, int $depth) use ($prefix) {
            $url = (string) ($node['url'] ?? '');
            $node['url'] = $prefix . $url;
        });
        return $tree;
    }

    /**
     * 读取类的 docblock（不存在/无注解返回 ''）
     */
    protected function getClassDoc(string $class): string
    {
        if ($class === '' || !class_exists($class)) {
            return '';
        }
        try {
            return (string) (new \ReflectionClass($class))->getDocComment();
        } catch (\Throwable $e) {
            return '';
        }
    }

    /**
     * 读取方法的 docblock（不存在/无注解返回 ''）
     */
    protected function getMethodDoc(string $class, string $method): string
    {
        if ($class === '' || !class_exists($class)) {
            return '';
        }
        try {
            return (string) (new \ReflectionMethod($class, $method))->getDocComment();
        } catch (\Throwable $e) {
            return '';
        }
    }

    /**
     * 解析 @tag 名称 [参数] 行：返回 [名称, 尾参(数字权重或url)]，无该注解返回 null
     * @return array{0: string, 1: string}|null
     */
    protected function parseAnnotatedLine(string $doc, string $tag): ?array
    {
        if (!preg_match('/@' . $tag . '\s+([^*\n]+)/', $doc, $m)) {
            return null;
        }
        $parts = preg_split('/\s+/', trim($m[1]));
        $name = (string) array_shift($parts);
        if ($name === '') {
            return null;
        }
        return [$name, (string) ($parts[0] ?? '')];
    }

    /**
     * 解析 @menu_weight N，缺省 0
     */
    protected function parseWeight(string $doc): int
    {
        if (preg_match('/@menu_weight\s+(-?\d+)/', $doc, $m)) {
            return (int) $m[1];
        }
        return 0;
    }

    /**
     * 从根节点到子孙节点遍历整个树，对每个节点执行回调函数
     *
     * @param array &$nodes 树形结构（引用传递，直接修改原树）
     * @param callable $callback 回调函数，签名为 function(array &$node, int $depth): void
     *                         - $node: 当前节点引用，可直接修改
     *                         - $depth: 当前深度，根节点为 0
     * @return array 返回修改后的树（与 $nodes 相同引用）
     */
    public function walkTree(array &$nodes, callable $callback): array
    {
        $this->walkTreeRecursive($nodes, $callback, 0);
        return $nodes;
    }

    /**
     * 递归遍历树的内部实现
     *
     * @param array &$nodes 节点数组（引用传递）
     * @param callable $callback 回调函数
     * @param int $depth 当前深度，根节点为 0
     */
    protected function walkTreeRecursive(array &$nodes, callable $callback, int $depth): void
    {
        foreach ($nodes as &$node) {
            $callback($node, $depth);
            if (!empty($node['children'])) {
                $this->walkTreeRecursive($node['children'], $callback, $depth + 1);
            }
        }
        unset($node);
    }

    /**
     * 把权限菜单树转换为侧边栏菜单树
     *
     * @param array $nodes 权限菜单树
     * @return array 侧边栏菜单树
     */
    public function permissionMenuTreeToSideMenuTree(array $nodes): array
    {
        $result = [];
        foreach ($nodes as $node) {
            $type = $node['type'] ?? 0;
            $isDirectory = ($type === 0);

            // 递归过滤 children
            $children = $node['children'] ?? [];
            if (!empty($children)) {
                $children = $this->permissionMenuTreeToSideMenuTree($children);
            }

            // type > 1 → 跳过
            if ($type > 1) {
                continue;
            }
            // type=0 且空 children → 跳过
            if ($isDirectory && empty($children)) {
                continue;
            }

            // 构建节点
            $item = [
                'name' => $node['name'] ?? '',
                'url' => $node['url'] ?? '',
                'icon' => $node['icon'] ?? null,
                'type' => $type,
            ];
            if (!empty($children)) {
                $item['children'] = $children;
            }
            $result[] = $item;
        }
        return $result;
    }

    /**
     * 把记录集（扁平数据）转换为树形结构
     *
     * @param array $recordset 记录集，每条记录包含 id 和 pid（或 parent_id）
     * @param string $idField id 字段名，默认 'id'
     * @param string $pidField 父 id 字段名，默认 'pid'
     * @param int $rootPid 根节点的父 id 值，默认 0
     * @return array 树形结构
     */
    public function recordsetToTree(array $recordset, string $idField = 'id', string $pidField = 'pid', int $rootPid = 0): array
    {
        // 构建 id => record 的映射
        $map = [];
        foreach ($recordset as $record) {
            $id = $record[$idField] ?? null;
            if ($id !== null) {
                $map[$id] = $record;
                $map[$id]['children'] = [];
            }
        }

        // 构建树
        $tree = [];
        foreach ($map as $id => &$node) {
            $pid = $node[$pidField] ?? $rootPid;
            if ($pid == $rootPid || !isset($map[$pid])) {
                $tree[] = &$node;
            } else {
                $map[$pid]['children'][] = &$node;
            }
        }
        unset($node);

        return $tree;
    }

    /**
     * 把树形结构转换为记录集（扁平数据）
     *
     * @param array $tree 树形结构
     * @param string $idField id 字段名，默认 'id'
     * @param string $pidField 父 id 字段名，默认 'pid'
     * @param int $rootPid 根节点的父 id 值，默认 0
     * @return array 记录集
     */
    public function treeToRecordset(array $tree, string $idField = 'id', string $pidField = 'pid', int $rootPid = 0): array
    {
        $recordset = [];
        $this->treeToRecordsetRecursive($tree, $recordset, $idField, $pidField, $rootPid);
        return $recordset;
    }

    /**
     * treeToRecordset 的递归实现
     *
     * @param array $nodes 节点数组
     * @param array &$recordset 记录集（引用）
     * @param string $idField id 字段名
     * @param string $pidField 父 id 字段名
     * @param int $pid 父 id
     */
    protected function treeToRecordsetRecursive(array $nodes, array &$recordset, string $idField, string $pidField, int $pid): void
    {
        foreach ($nodes as $node) {
            $id = $node[$idField] ?? null;
            $record = $node;
            unset($record['children']);
            $record[$pidField] = $pid;
            $recordset[] = $record;
            if (!empty($node['children'])) {
                $this->treeToRecordsetRecursive($node['children'], $recordset, $idField, $pidField, $id ?? 0);
            }
        }
    }
}
