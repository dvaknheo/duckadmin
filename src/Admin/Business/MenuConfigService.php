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
     * 一键扫描：委托给 AdminTreeBuilder::build()
     * @return array 树形精简菜单结构
     */
    public function scanRoutes(): array
    {
        return (new AdminTreeBuilder())->build();
    }

    // ================================================================
    //  导入：树形精简结构 → admin_permissions 表
    // ================================================================

    /**
     * 安装时调用：优先读取 config/scanned_menu.php，为空则扫描路由，然后导入数据库
     * @param string $prefix 挂载前缀，如 /admin/
     * @return array<string> 新增的菜单/操作 url 列表
     */
    public function installMenus(string $prefix): array
    {
        $menuTree = $this->loadScannedMenu();
        if (empty($menuTree)) {
            $menuTree = $this->scanRoutes();
        }
        // 先补全绝对 url
        $menuTree = (new AdminTreeBuilder())->resolveUrls($menuTree, $prefix);
        return $this->importToDb($menuTree);
    }

    /**
     * 把树形精简结构导入到 admin_permissions 表（幂等，按 url 判重）
     * 传入的 $menuTree 应已补全绝对 url（通过 resolveUrls）
     *
     * @param array $menuTree 树形精简菜单结构（url 已补全）
     * @return array<string> 新增的菜单/操作 url 列表（目录静默创建不计入）
     */
    public function importToDb(array $menuTree): array
    {
        return PermissionModel::_()->importMenu($menuTree);
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
     * 精简树：委托给 AdminTreeBuilder::simplifyTree()
     */
    protected function simplifyTree(array $nodes): array
    {
        return (new AdminTreeBuilder())->simplifyTree($nodes);
    }
}
