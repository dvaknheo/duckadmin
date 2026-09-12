<?php declare(strict_types=1);
/**
 * DuckPhp Admin System - Menu Config Service
 * 菜单配置服务：扫描路由注解生成树形精简结构，导入数据库，构建用户菜单树
 *
 * 与 PermissionService 的区别：
 * - PermissionService：权限校验（checkUserUrl）、权限 id 查询
 * - MenuConfigService：菜单结构的扫描、导入、导出、构建
 */
namespace DuckAdmin\Admin\Business;

use DuckAdmin\Admin\Model\PermissionModel;
use DuckAdmin\Admin\Model\RoleUserModel;

class MenuConfigService extends Base
{
    // ================================================================
    //  扫描：路由注解 → 树形精简结构（不入库）
    // ================================================================

    /**
     * 一键扫描：RouteLister 扫描路由，按注解生成 分组→目录→菜单/操作 树形结构
     *
     * 注解说明（均写在紧贴 class/方法的 docblock 内）：
     * - 类上 @menu_group 名称 [权重]      顶级目录（namespace 下分组），无则整个控制器不扫
     * - 类上 @menu_directory 名称 [url]   目录（每控制器一个）；url 为"当前控制器无匹配菜单时"要高亮的菜单
     * - 类上 @menu_weight N               目录的本地权重 d
     * - 方法上 @menu_item 名称            菜单（type=1），url 取路由的完整 path（无域名）
     * - 方法上 @menu_action 名称          操作（type=2）
     * - 方法上 @menu_weight N             菜单/操作的本地权重 w
     *
     * 返回结构（无 id、无 weight、url 为相对地址）：
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
    public function scanRoutes(): array
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
                continue; // 无 @menu_group 的控制器不扫描
            }
            [$groupName] = $group;
            $dir = $this->parseAnnotatedLine($doc, 'menu_directory');

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

            // 目录节点
            if ($dir !== null) {
                [$dirName, $dirUrl] = $dir;
                $dirKey = $groupName . "\0" . $dirName;
                if (!isset($dirMap[$dirKey])) {
                    $tree[$groupIdx]['children'][] = [
                        'name' => $dirName,
                        'icon' => null,
                        'url' => $dirUrl, // 相对地址，导入时补全
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

    // ================================================================
    //  导入：树形精简结构 → admin_permissions 表
    // ================================================================

    /**
     * 安装时调用：优先读取 config/scanned_menu.php，为空则扫描路由，然后导入数据库
     * @return array<string> 新增的菜单/操作 url 列表
     */
    public function installMenus(): array
    {
        $menuTree = $this->loadScannedMenu();
        if (empty($menuTree)) {
            $menuTree = $this->scanRoutes();
        }
        return $this->importToDb($menuTree);
    }

    /**
     * 把树形精简结构导入到 admin_permissions 表（幂等，按 url 判重）
     *
     * 自动补全：
     * - 相对 url → 绝对 url（补挂载前缀）
     * - 目录 url 追加 # 后缀（高亮别名）
     * - parent_id、weight（按层级和顺序自动计算）
     *
     * @param array $menuTree 树形精简菜单结构（scanRoutes() 的返回格式）
     * @return array<string> 新增的菜单/操作 url 列表（目录静默创建不计入）
     */
    public function importToDb(array $menuTree): array
    {
        $model = PermissionModel::_();
        $existing = [];
        foreach ($model->getAll() as $p) {
            $existing[$p['url']] = (int)$p['id'];
        }

        $added = [];
        $seq = 0; // 全局递增序号，用于计算 weight

        $import = function (array $nodes, int $parentId, int $baseWeight) use (&$import, $model, &$existing, &$added, &$seq) {
            foreach ($nodes as $node) {
                $seq++;
                // 优先使用节点自带的 weight，否则按顺序递增
                $weight = isset($node['weight']) ? $baseWeight + (int)$node['weight'] : $baseWeight + $seq;

                // 补全 url
                $url = (string)($node['url'] ?? '');
                if ($url !== '' && $url[0] !== '/') {
                    $url = $this->resolvePrefix() . ltrim($url, '/');
                }
                $type = (int)($node['type'] ?? 1);
                if ($type === 0 && $url !== '') {
                    // 目录 url：去掉 basename 后加 #，如 /admin/Admin/index → /admin/Admin/#
                    $url = rtrim($url, '/');
                    $pos = strrpos($url, '/');
                    if ($pos !== false) {
                        $url = substr($url, 0, $pos + 1) . '#';
                    } else {
                        $url .= '#';
                    }
                }

                if (isset($existing[$url])) {
                    $id = $existing[$url];
                } else {
                    $id = $model->create([
                        'name' => (string)$node['name'],
                        'url' => $url,
                        'type' => $type,
                        'parent_id' => $parentId,
                        'weight' => $weight,
                        'source' => 1,
                    ]);
                    $existing[$url] = $id;
                    if ($type !== 0) {
                        $added[] = $url;
                    }
                }

                if (!empty($node['children'])) {
                    $import($node['children'], $id, $weight * 100);
                }
            }
        };

        $import($menuTree, 0, 0);
        return $added;
    }

    // ================================================================
    //  构建：数据库 → 用户可见菜单树（精简结构，无 type=2）
    // ================================================================

    /**
     * 从 DB 构建用户可见菜单树（过滤 type=2 操作，精简结构）
     *
     * 返回结构：
     *   [
     *     'name' => '系统管理', 'icon' => null, 'url' => '',
     *     'children' => [
     *       ['name' => '人员管理', 'icon' => null, 'url' => '/admin/Admin/index', 'children' => []],
     *     ],
     *   ]
     *
     * @param int $userId 用户 id
     * @return array 用户可见菜单树
     */
    public function buildUserMenuTree(int $userId): array
    {
        if (RoleUserModel::_()->isSuperRole($userId)) {
            $rows = PermissionModel::_()->getAllMenuItems();
        } else {
            $rows = PermissionModel::_()->getMenuItemsByUser($userId);
        }

        // 组树
        $map = [];
        foreach ($rows as $row) {
            $row['children'] = [];
            $map[$row['id']] = $row;
        }
        $tree = [];
        foreach ($map as $id => &$node) {
            $pid = (int)$node['parent_id'];
            if ($pid && isset($map[$pid])) {
                $map[$pid]['children'][] = &$node;
            } else {
                $tree[] = &$node;
            }
        }
        unset($node);

        // 精简：去掉 type 字段和空 children，过滤 type=2（已在 SQL 中过滤）
        return $this->simplifyTree($tree);
    }

    // ================================================================
    //  配置文件的读写
    // ================================================================

    /**
     * 读取 config/scanned_menu.php
     * @return array 树形精简菜单结构，文件不存在或为空返回 []
     */
    public function loadScannedMenu(): array
    {
        $file = $this->getScannedMenuPath();
        if (!is_file($file)) {
            return [];
        }
        $data = require $file;
        return is_array($data) ? $data : [];
    }

    /**
     * 把树形精简结构写入 config/scanned_menu.php（供对比用）
     */
    public function saveScannedMenu(array $menuTree): bool
    {
        $file = $this->getScannedMenuPath();
        $export = var_export($menuTree, true);
        $content = "<?php\n// 扫描生成的菜单结构（树形，无 id/weight，url 为相对地址）\n"
            . "// 由 MenuConfigService::scanRoutes() 生成，供对比和手动调整\n"
            . "// 安装时由 MenuConfigService::installMenus() 读取并导入数据库\n"
            . "return {$export};\n";
        return file_put_contents($file, $content) !== false;
    }

    /**
     * 读取指定路径的 config/menu.php（各子工程可共用此格式）
     */
    public function loadMenuConfig(string $path): array
    {
        if (!is_file($path)) {
            return [];
        }
        $data = require $path;
        return is_array($data) ? $data : [];
    }

    // ================================================================
    //  内部辅助方法
    // ================================================================

    /**
     * config/scanned_menu.php 的绝对路径
     */
    protected function getScannedMenuPath(): string
    {
        return __DIR__ . '/../config/scanned_menu.php';
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
     * 精简树：去掉 type 字段和空 children，保留 name/icon/url/children
     */
    protected function simplifyTree(array $nodes): array
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
