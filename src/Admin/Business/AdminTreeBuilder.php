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
    /**
     * 构建菜单树：RouteLister 扫描路由，按注解生成 分组→目录→菜单/操作 树形结构
     *
     * 注解说明（均写在紧贴 class/方法的 docblock 内）：
     * - 类上 @menu_group 名称 [权重]      顶级目录（namespace 下分组），无则整个控制器不扫
     * - 类上 @menu_directory 名称 [url]   目录（每控制器一个）；url 为"当前控制器无匹配菜单时"要高亮的菜单
     * - 类上 @menu_weight N               目录的本地权重 d
     * - 方法上 @menu_item 名称            菜单（type=1），url 取路由的完整 path（无域名）
     * - 方法上 @menu_action 名称          操作（type=2）
     * - 方法上 @menu_weight N             菜单/操作的本地权重 w
     *
     * 规则：
     * - 公开方法没有 @menu_action 时，默认视作 action，名字为方法名
     * - @menu_weight 越大排序越靠前
     * - 返回结构无 id、无 weight、url 为相对地址
     *
     * 返回结构：
     *   [
     *     'name' => '系统管理', 'icon' => null, 'url' => '', 'type' => 0,
     *     'children' => [
     *       [
     *         'name' => '人员管理', 'icon' => null, 'url' => 'Admin/index', 'type' => 1,
     *         'children' => [
     *           ['name' => '新增人员', 'url' => 'Admin/create', 'type' => 2, 'children' => []],
     *         ],
     *       ],
     *     ],
     *   ]
     *
     * @return array 树形精简菜单结构
     */
    public function build(): array
    {
        $routes = \DuckPhp\Component\RouteLister::_()->listAll(true, true, true);
        $groups = [];
        foreach ($routes as $route) {
            $controller = (string)($route['controller'] ?? '');
            $method = (string)($route['method'] ?? '');
            $url = (string)($route['url'] ?? '');
            if ($controller === '' || $method === '' || $url === '') {
                continue;
            }
            $path = '/' . ltrim($url, '/');
            if ($path === '/') {
                continue;
            }
            $groups[$controller][$method] = $path;
        }

        $tree = [];
        $groupMap = [];   // 组名 => 在 $tree 中的下标
        $dirMap = [];     // "组名\0目录名" => 在对应 children 中的下标

        foreach ($groups as $controller => $methods) {
            $doc = $this->getClassDoc($controller);
            if ($doc === '') {
                continue;
            }
            $group = $this->parseAnnotatedLine($doc, 'menu_group');
            if ($group === null) {
                // 无 @menu_group 时挂到第一个顶级目录
                if (empty($tree)) {
                    continue; // 还没有任何顶级目录，跳过
                }
                $groupIdx = 0;
            } else {
                [$groupName] = $group;
                // 分组节点
                if (!isset($groupMap[$groupName])) {
                    $tree[] = [
                        'name' => $groupName,
                        'icon' => null,
                        'url' => '',
                        'type' => 0,
                        'children' => [],
                    ];
                    $groupMap[$groupName] = count($tree) - 1;
                }
                $groupIdx = $groupMap[$groupName];
            }
            $dir = $this->parseAnnotatedLine($doc, 'menu_directory');

            // 目录节点
            if ($dir !== null) {
                [$dirName, $dirUrl] = $dir;
                $dirKey = $groupIdx . "\0" . $dirName;
                if (!isset($dirMap[$dirKey])) {
                    // 目录 url：去掉 basename 后加 #，如 Admin/index → Admin/#
                    $dirUrl = rtrim($dirUrl, '/');
                    $pos = strrpos($dirUrl, '/');
                    if ($pos !== false) {
                        $dirUrl = substr($dirUrl, 0, $pos + 1) . '#';
                    } elseif ($dirUrl !== '') {
                        $dirUrl .= '#';
                    }
                    $tree[$groupIdx]['children'][] = [
                        'name' => $dirName,
                        'icon' => null,
                        'url' => $dirUrl,
                        'type' => 0,
                        'children' => [],
                    ];
                    $dirMap[$dirKey] = count($tree[$groupIdx]['children']) - 1;
                }
                $dirIdx = $dirMap[$dirKey];
                $parentRef = &$tree[$groupIdx]['children'][$dirIdx];
            } else {
                // 无目录时，菜单/操作直接挂分组下
                $parentRef = &$tree[$groupIdx];
            }

            // 方法注解 → 菜单/操作节点
            foreach ($methods as $method => $path) {
                $mDoc = $this->getMethodDoc($controller, $method);
                $item = $this->parseAnnotatedLine($mDoc, 'menu_item');
                $action = $item === null ? $this->parseAnnotatedLine($mDoc, 'menu_action') : null;
                $anno = $item ?? $action;
                if ($anno === null) {
                    // 公开方法无注解时，默认视作 action，名字为方法名
                    $ref = new \ReflectionMethod($controller, $method);
                    if (!$ref->isPublic() || strpos($method, '_') === 0) {
                        continue;
                    }
                    $anno = [$method, ''];
                    $action = $anno;
                }
                $parentRef['children'][] = [
                    'name' => $anno[0],
                    'url' => $this->toRelativePath($path, $methods),
                    'type' => $item !== null ? 1 : 2,
                    '_weight' => $this->parseWeight($mDoc), // 临时字段，排序后移除
                    'children' => [],
                ];
            }
            unset($parentRef);
        }

        // 排序：weight 越大越靠前
        $this->sortTree($tree);

        return $tree;
    }

    /**
     * 解析当前应用的 url 挂载前缀（如 /admin/）
     * 从所有路由的最长公共前缀推断
     */
    protected function resolvePrefix(): string
    {
        $routes = \DuckPhp\Component\RouteLister::_()->listAll(true, true, true);
        $paths = [];
        foreach ($routes as $route) {
            $url = (string)($route['url'] ?? '');
            if ($url !== '') {
                $paths[] = '/' . ltrim($url, '/');
            }
        }
        if (empty($paths)) {
            return '/';
        }
        // 找所有路由的公共前缀
        $prefix = $paths[0];
        foreach ($paths as $path) {
            $len = min(strlen($prefix), strlen($path));
            $i = 0;
            while ($i < $len && $prefix[$i] === $path[$i]) {
                $i++;
            }
            $prefix = substr($prefix, 0, $i);
        }
        // 确保以 / 结尾
        if (substr($prefix, -1) !== '/') {
            $pos = strrpos($prefix, '/');
            if ($pos !== false) {
                $prefix = substr($prefix, 0, $pos + 1);
            } else {
                $prefix = '/';
            }
        }
        return $prefix;
    }

    /**
     * 把绝对 path 转为相对地址（去掉挂载前缀）
     * 如 /admin/Admin/index → Admin/index
     */
    protected function toRelativePath(string $fullPath, array $methods): string
    {
        // 用 resolvePrefix 获取挂载前缀（如 /admin/）
        $prefix = $this->resolvePrefix();
        if (strpos($fullPath, $prefix) === 0) {
            return substr($fullPath, strlen($prefix));
        }
        // 回退：去掉第一个路径段
        $parts = explode('/', ltrim($fullPath, '/'));
        array_shift($parts);
        return implode('/', $parts);
    }

    /**
     * 递归排序树：按 _weight 临时字段，weight 越大越靠前
     * 排序后从节点中移除 _weight 字段
     */
    protected function sortTree(array &$nodes): void
    {
        // 先收集每个节点的 _weight，然后移除
        $weights = [];
        foreach ($nodes as $idx => &$node) {
            $weights[$idx] = (int)($node['_weight'] ?? 0);
            unset($node['_weight']);
        }
        unset($node);
        
        // 按 weight 降序排序
        uksort($nodes, function ($a, $b) use ($weights) {
            return $weights[$b] <=> $weights[$a];
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
}
