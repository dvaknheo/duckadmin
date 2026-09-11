<?php declare(strict_types=1);
/**
 * DuckPhp Admin System - Permission Service
 * 权限业务服务（业务逻辑层）
 */
namespace DuckAdmin\Admin\Business;

use DuckAdmin\Admin\Model\PermissionModel;
use DuckAdmin\Admin\Model\RoleUserModel;

class PermissionService extends Base
{
    /**
     * 用户已拥有的权限 id 集合(超管=全部权限)
     */
    public function getUserPermissionIds(int $userId): array
    {
        if (RoleUserModel::_()->isSuperRole($userId)) {
            return PermissionModel::_()->getAllIds();
        } else {
            return PermissionModel::_()->getUserPermissionIdsByRoles($userId);
        }
    }

    /**
     * 用户是否拥有指定 url 的权限(超管全放行)
     * url 与库中存储一致:无域名的完整 path(含挂载前缀,如 /admin/Role/index)
     */
    public function checkUserUrl(int $userId, string $url): bool
    {
        if (RoleUserModel::_()->isSuperRole($userId)) {
            return true;
        }
        $path = (string)(parse_url($url, PHP_URL_PATH) ?: $url);
        $path = '/' . ltrim($path, '/');
        if ($path === '/' || $path === '/index' || preg_match('#(^|/)Home/index$#', $path)) {
            return true; // 首页/仪表盘放行
        }
        if (preg_match('#(^|/)Role/permissions$#', $path)) {
            return true; // 分配权限页:访问由 RoleController 内部按职位管理范围控制
        }
        $count = PermissionModel::_()->countUserUrlPermissions($userId, $path);
        return $count > 0;
    }

    /**
     * 获取用户可见菜单树(type 0/1 按 parent_id 组树)
     * 超级管理员职位直接返回全部菜单,其余按职位规则过滤
     */
    public function getUserMenus(int $userId): array
    {
        if (RoleUserModel::_()->isSuperRole($userId)) {
            $rows = PermissionModel::_()->getAllMenuItems();
        } else {
            $rows = PermissionModel::_()->getMenuItemsByUser($userId);
        }
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
        return $tree;
    }

    /**
     * 一键扫描:RouteLister 扫描路由,按注解生成 分组→目录→菜单/操作 四级权限
     *
     * 注解说明(均写在紧贴 class/方法的 docblock 内):
     * - 类上 @menu_group 名称 [权重]      顶级目录(namespace 下分组),无则整个控制器不扫
     * - 类上 @menu_directory 名称 [url]   目录(每控制器一个);url 为"当前控制器无匹配菜单时"要高亮的菜单
     *                                     (写应用相对路径如 Admin/index,入库时按实际路由补全为完整 path,
     *                                     并以 # 结尾保持 url 唯一,仅作高亮别名)
     * - 类上 @menu_weight N               目录的本地权重 d
     * - 方法上 @menu_item 名称            菜单(type=1),url 取路由的完整 path(无域名)
     * - 方法上 @menu_action 名称          操作(type=2)
     * - 方法上 @menu_weight N             菜单/操作的本地权重 w
     *
     * 权重公式(权重越大越靠后,缺省 0):
     *   分组 Wg = g * 10000
     *   目录 Wd = d * 1000 + Wg
     *   菜单/操作 = w + Wd
     *
     * 幂等: 以 url 判重,已入库跳过;分组/目录按 名称+父级 复用
     * @return array<string> 新增的菜单/操作 url 列表(分组/目录静默创建不计入)
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
            $path = '/' . ltrim($url, '/'); // 无域名的完整 path
            if ($path === '/') {
                continue;
            }
            $groups[$controller][$method] = $path;
        }

        $model = PermissionModel::_();
        $existing = [];
        foreach ($model->getAll() as $p) {
            $existing[$p['url']] = (int)$p['id'];
        }
        $added = [];
        $groupIds = [];   // 组名 => [id, Wg]
        $dirIds = [];     // 组名\0目录名 => [id, Wd]

        foreach ($groups as $controller => $methods) {
            $doc = $this->getClassDoc($controller);
            if ($doc === '') {
                continue;
            }
            $group = $this->parseAnnotatedLine($doc, 'menu_group'); // [name, weight]
            if ($group === null) {
                continue; // 无 @menu_group 的控制器不扫描
            }
            [$groupName, $g] = $group;
            $g = (int)$g; // 尾参为组权重,缺省 0
            $dir = $this->parseAnnotatedLine($doc, 'menu_directory'); // [name, url]
            $d = $this->parseWeight($doc);

            // 分组(顶级目录)
            $wg = $g * 10000;
            if (!isset($groupIds[$groupName])) {
                $gid = $model->findDirectoryId($groupName, 0);
                if (!$gid) {
                    $gid = $model->create([
                        'name' => $groupName, 'url' => '', 'type' => 0,
                        'parent_id' => 0, 'weight' => $wg, 'source' => 1,
                    ]);
                }
                $groupIds[$groupName] = [$gid, $wg];
            }
            [$groupId, $wg] = $groupIds[$groupName];

            // 目录(每控制器一个,可缺省;缺省时菜单/操作直接挂分组下)
            // 目录 url 仅作"高亮别名"(当前控制器无匹配菜单时要高亮的菜单 url),
            // 以 # 结尾保证 url 全局唯一,不参与真实路由匹配
            $parentId = $groupId;
            $wd = $wg;
            if ($dir !== null) {
                [$dirName, $dirUrl] = $dir;
                $wd = $d * 1000 + $wg;
                $key = $groupName . "\0" . $dirName;
                if (!isset($dirIds[$key])) {
                    $fullDirUrl = $this->toFullPath($dirUrl, $methods);
                    if ($fullDirUrl !== '') {
                        $fullDirUrl .= '#';
                    }
                    $did = $model->findDirectoryId($dirName, $groupId);
                    if (!$did) {
                        $did = $model->create([
                            'name' => $dirName, 'url' => $fullDirUrl, 'type' => 0,
                            'parent_id' => $groupId, 'weight' => $wd, 'source' => 1,
                        ]);
                    }
                    $dirIds[$key] = [$did, $wd];
                }
                [$parentId, $wd] = $dirIds[$key];
            }

            // 方法: @menu_item 菜单 / @menu_action 操作(同写则 item 优先)
            foreach ($methods as $method => $path) {
                if (isset($existing[$path])) {
                    continue;
                }
                $mDoc = $this->getMethodDoc($controller, $method);
                if ($mDoc === '') {
                    continue;
                }
                $item = $this->parseAnnotatedLine($mDoc, 'menu_item');
                $action = $item === null ? $this->parseAnnotatedLine($mDoc, 'menu_action') : null;
                $anno = $item ?? $action;
                if ($anno === null) {
                    continue;
                }
                $model->create([
                    'name' => $anno[0],
                    'url' => $path,
                    'type' => $item !== null ? 1 : 2,
                    'parent_id' => (int)$parentId,
                    'weight' => $this->parseWeight($mDoc) + $wd,
                    'source' => 1,
                ]);
                $existing[$path] = true;
                $added[] = $path;
            }
        }
        return $added;
    }

    /**
     * 把注解里的应用相对路径补全为完整 path:取该控制器任一路由的挂载前缀
     * 如注解写 Admin/index, 路由为 /admin/Admin/index => /admin/Admin/index
     */
    protected function toFullPath(string $url, array $methods): string
    {
        $url = trim($url);
        if ($url === '') {
            return '';
        }
        $url = '/' . ltrim($url, '/');
        foreach ($methods as $path) {
            $pos = strpos($path, $url);
            if ($pos !== false && $pos + strlen($url) === strlen($path)) {
                return substr($path, 0, $pos) . $url; // 补上挂载前缀
            }
        }
        return $url;
    }

    /**
     * 读取类的 docblock(不存在/无注解返回 '')
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
     * 读取方法的 docblock(不存在/无注解返回 '')
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
     * 解析 @tag 名称 [参数] 行:返回 [名称, 尾参(数字权重或url)],无该注解返回 null
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
     * 解析 @menu_weight N,缺省 0
     */
    protected function parseWeight(string $doc): int
    {
        if (preg_match('/@menu_weight\s+(-?\d+)/', $doc, $m)) {
            return (int)$m[1];
        }
        return 0;
    }
}
