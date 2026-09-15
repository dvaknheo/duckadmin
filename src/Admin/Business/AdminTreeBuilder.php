<?php declare(strict_types=1);
/**
 * DuckPhp Admin System - Admin Tree Builder
 * 扫描路由注解，构建后台管理菜单树（精简结构）
 *
 * 与 MenuConfigService 的分工：
 * - AdminTreeBuilder：只负责扫描路由生成树形结构
 * - MenuConfigService：负责导入数据库、构建用户菜单、读写配置文件
 */
namespace DuckAdmin\Admin\Business;

class AdminTreeBuilder
{
    protected function getRoutes()
    {
        $routes = \DuckPhp\Component\RouteLister::_()->listAll(false, true, true);
        // 转换为相对地址
        $prefix = $this->getUrlPrefix();
        foreach ($routes as &$route) {
            $url = (string)($route['url'] ?? '');
            if ($prefix && strpos($url, $prefix) === 0) {
                $route['url'] = substr($url, strlen($prefix));
            }
        }
        unset($route);
        return $routes;
    }

    protected function getUrlPrefix(): string
    {
        return (string)(\DuckPhp\Core\App::_()->options['controller_url_prefix'] ?? '');
    }

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
     * - @menu_weight N           本层权重，越大越靠前
     *
     * 方法层级注解：
     * - @menu_directory 名称 可选 顶级分组， 支持 \ 切分生成多级目录 ，插入相应的目录
     * - @menu 名称               菜单（type=1），url 取路由完整 path
     * - @menu_action 名称        action（type=2）
     * - @menu_permission #url 名称   权限项（type=3），#url 会被加上方法 url 前缀
     * - @menu_weight N           本层权重
     * - 公开方法无任何注解       视为 action（type=2），名称为方法名
     *
     * 后处理：切分子层级、排序、simplifyTree 精简结构
     *
     * @param string $prefix 挂载前缀，如 /admin/
     * @return array[] 树形精简菜单结构
     */
    public function build(string $prefix): array
    {
        $routes = $this->getRoutes(); // url 已经是相对地址

        // 1. 按 controller 分组
        $controllers = [];
        foreach ($routes as $route) {
            $controller = (string)($route['controller'] ?? '');
            $method = (string)($route['method'] ?? '');
            $url = (string)($route['url'] ?? '');
            if ($controller === '' || $method === '') {
                continue;
            }
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
            if ($classDoc === '') {
                continue;
            }

            // class 层级的目录注解和权重
            $dirAnno = $this->parseAnnotatedLine($classDoc, 'menu_directory');
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
                    'name' => $dirAnno ? $dirAnno[0] : '',
                    'url' => $dirUrl,
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

        // 5. 精简结构
        return $this->simplifyTree($tree);
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
            $name = (string)($item['name'] ?? '');
            $type = (int)($item['type'] ?? 1);
            $url = (string)($item['url'] ?? '');
            $weight = (int)($item['weight'] ?? 0);

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
            $first = (string)array_shift($parts);
            $second = (string)($parts[0] ?? '');
            $results[] = [$first, $second];
        }
        return $results;
    }

    /**
     * 切分子层级（处理 \ 分割的多级目录）
     * 如 "系统管理\用户管理" → 系统管理 → 用户管理 → (children)
     *
     * @param array $tree 原始树
     * @return array 处理后的树
     */
    protected function splitSubLevels(array $tree): array
    {
        $result = [];

        foreach ($tree as $node) {
            $parts = explode('\\', $node['name']);
            if (count($parts) === 1) {
                // 没有 \ 分割，直接保留
                $result[] = $node;
            } else {
                // 多级目录，递归创建
                $result = $this->mergeSubLevel($result, $parts, 0, $node);
            }
        }

        return $result;
    }

    /**
     * 合并子层级节点
     *
     * @param array $nodes 当前层级的节点数组
     * @param array $parts 名称分割后的数组
     * @param int $idx 当前处理的 parts 索引
     * @param array $child 原始节点（要挂载在最深层级）
     * @return array 合并后的节点数组
     */
    protected function mergeSubLevel(array $nodes, array $parts, int $idx, array $child): array
    {
        $name = $parts[$idx];
        $isLast = ($idx === count($parts) - 1);

        // 查找或创建当前层级的节点
        $found = false;
        foreach ($nodes as &$node) {
            if ($node['name'] === $name && $node['type'] === 0) {
                // 找到匹配的目录节点
                if ($isLast) {
                    // 最后一个部分，将 child 作为子节点添加
                    $node['children'] = array_merge($node['children'], $child['children']);
                    if (!empty($child['children'])) {
                        foreach ($child['children'] as $c) {
                            $node['children'][] = $c;
                        }
                    } else {
                        // 如果没有 children，说明当前就是叶子节点
                        $node['children'][] = $child;
                    }
                } else {
                    // 继续向下递归
                    $node['children'] = $this->mergeSubLevel($node['children'], $parts, $idx + 1, $child);
                }
                $found = true;
                break;
            }
        }
        unset($node);

        if (!$found) {
            // 需要创建新的目录节点
            $newNode = [
                'name' => $name,
                'url' => $isLast ? ($child['url'] ?? null) : null,
                'type' => 0,
                'children' => [],
            ];
            if ($isLast) {
                // 最后一个部分，添加原始子节点
                $childNodes = $child['children'] ?? [];
                if (!empty($childNodes)) {
                    $newNode['children'] = $childNodes;
                } else {
                    // 没有 children，将当前节点作为子节点
                    $newNode['children'][] = $child;
                }
            } else {
                // 继续向下递归创建
                $newNode['children'] = $this->mergeSubLevel([], $parts, $idx + 1, $child);
            }
            $nodes[] = $newNode;
        }

        return $nodes;
    }

    /**
     * 把绝对 path 转为相对地址（去掉挂载前缀）
     * 如 /admin/Admin/index → Admin/index
     */
    protected function toRelativePath(string $fullPath, string $prefix): string
    {
        if (strpos($fullPath, $prefix) === 0) {
            return substr($fullPath, strlen($prefix));
        }
        // 回退：去掉第一个路径段
        $parts = explode('/', ltrim($fullPath, '/'));
        array_shift($parts);
        return implode('/', $parts);
    }

    /**
     * 递归排序树：同级按 weight 排序，weight 越大越靠前
     *
     * @param array &$nodes 树节点数组（引用传递）
     */
    protected function sortTree(array &$nodes): void
    {
        // 按 weight 降序排序（同级排序）
        uasort($nodes, function ($a, $b) {
            return ((int)($b['weight'] ?? 0)) <=> ((int)($a['weight'] ?? 0));
        });

        // 重新索引为连续数组
        $nodes = array_values($nodes);

        // 递归排序 children
        foreach ($nodes as &$node) {
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
        $this->walkTree($tree, function (array &$node, int $depth) use ($prefix) {
            $url = (string)($node['url'] ?? '');
            if ($url !== '' && $url[0] !== '/') {
                $node['url'] = $prefix . ltrim($url, '/');
            }
        });
        return $tree;
    }

    /**
     * 精简树：去掉 type 字段和空 children，保留 name/icon/url/children
     * 公开方法，供外部调用
     */
    public function simplifyTree(array $nodes): array
    {
        $result = [];
        foreach ($nodes as $node) {
            $item = [
                'name' => $node['name'],
                'icon' => $node['icon'] ?? null,
                'url' => $node['url'] ?? '',
            ];
            if (!empty($node['children'])) {
                $item['children'] = $this->simplifyTree($node['children']);
            } else {
                $item['children'] = [];
            }
            $result[] = $item;
        }
        return $result;
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
            return (string)(new \ReflectionClass($class))->getDocComment();
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
            return (string)(new \ReflectionMethod($class, $method))->getDocComment();
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
        $name = (string)array_shift($parts);
        if ($name === '') {
            return null;
        }
        return [$name, (string)($parts[0] ?? '')];
    }

    /**
     * 解析 @menu_weight N，缺省 0
     */
    protected function parseWeight(string $doc): int
    {
        if (preg_match('/@menu_weight\s+(-?\d+)/', $doc, $m)) {
            return (int)$m[1];
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
}
