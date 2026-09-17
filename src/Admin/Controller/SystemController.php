<?php declare(strict_types=1);
namespace DuckAdmin\Admin\Controller;

use DuckAdmin\Admin\Business\MenuBusiness;
use DuckAdmin\Admin\Model\PermissionModel;

/**
 * @menu_directory 系统管理
 * @menu_weight 1001
 */
class SystemController extends Base
{
    /**
     * 读取查看方式(GET/POST 均可): all=全部 menu=不看"操作"
     */
    protected function showMode(): string
    {
        $show = Helper::POST('show', '') ?: Helper::GET('show', 'all');
        return $show === 'menu' ? 'menu' : 'all';
    }

    /**
     * 列表页 url(携带 show 参数,可附加 error 等额外 query)
     */
    protected function listUrl(string $show, string $extra = ''): string
    {
        return __url('System/menu') . '?show=' . $show . $extra;
    }

    /**
     * @menu 菜单管理
     */
    public function menu()
    {
        $show = $this->showMode();

        $data['tree'] = MenuBusiness::_()->getTree($show);
        $data['show'] = $show;
        $data['title'] = '权限和菜单管理';

        $data['urls'] = [
            'list' => __url('System/menu'),
            'create' => __url('System/menu_create') . '?show=' . $show,
            'edit' => __url('System/menu_edit'),
            'delete' => __url('System/menu_delete'),
            'scan' => __url('System/menu_scan') . '?show=' . $show,
        ];

        Helper::Show($data, 'Menu/index');
    }

    /**
     * @menu_action 一键扫描
     */
    public function menu_scan()
    {
        $show = $this->showMode();

        // 一步操作：扫描生成树形结构，补全 url，导入数据库
        $menuTree = (new \DuckAdmin\Admin\Business\AdminTreeBuilder())->build(__url(''));
        $menuTree = (new \DuckAdmin\Admin\Business\AdminTreeBuilder())->resolveUrls($menuTree, __url(''));
        $added = PermissionModel::_()->importMenu($menuTree);
        // 保存扫描结果到 config/scanned_menu.php 供对比
        $this->saveScannedMenu($menuTree);

        $data['added'] = $added;
        $data['menuTree'] = $menuTree;
        $data['title'] = '一键扫描结果';
        $data['urls'] = [
            'list' => $this->listUrl($show),
        ];
        Helper::Show($data, 'Menu/scan');
    }

    /**
     * @menu_action 新增菜单
     */
    public function menu_create()
    {
        $show = $this->showMode();

        $data['show'] = $show;
        $data['title'] = '新增菜单';
        $data['perm_tree'] = MenuBusiness::_()->getTree('all');
        $data['urls'] = [
            'save' => __url('System/menu_save'),
            'list' => $this->listUrl($show),
        ];
        $data['is_edit'] = false;
        Helper::Show($data, 'Menu/form');
    }

    /**
     * @menu_action 保存菜单
     */
    public function menu_save()
    {
        $show = $this->showMode();
        $input = [
            'name' => Helper::POST('name', ''),
            'url' => Helper::POST('url', ''),
            'type' => (int)Helper::POST('type', '1'),
            'parent_id' => (int)Helper::POST('parent_id', '0'),
            'weight' => (int)Helper::POST('weight', '0'),
        ];

        try {
            MenuBusiness::_()->create($input);
            Helper::Show302($this->listUrl($show));
            return;
        } catch (\Exception $ex) {
            $data['error'] = $ex->getMessage();
        }
        $data['show'] = $show;
        $data['title'] = '新增菜单';
        $data['input'] = $input;
        $data['perm_tree'] = MenuBusiness::_()->getTree('all');
        $data['urls'] = [
            'save' => __url('System/menu_save'),
            'list' => $this->listUrl($show),
        ];
        $data['is_edit'] = false;
        Helper::Show($data, 'Menu/form');
    }

    /**
     * @menu_action 编辑菜单
     */
    public function menu_edit()
    {
        $show = $this->showMode();
        $id = (int)Helper::GET('id', '0');
        $perm = MenuBusiness::_()->getById($id);
        if (!$perm) {
            Helper::Show302($this->listUrl($show));
            return;
        }

        $data['perm'] = $perm;
        $data['show'] = $show;
        $data['title'] = '编辑菜单';
        $data['perm_tree'] = MenuBusiness::_()->getTree('all');
        $data['urls'] = [
            'update' => __url('System/menu_update'),
            'list' => $this->listUrl($show),
        ];
        $data['is_edit'] = true;
        Helper::Show($data, 'Menu/form');
    }

    /**
     * @menu_action 更新菜单
     */
    public function menu_update()
    {
        $show = $this->showMode();
        $id = (int)Helper::POST('id', '0');
        $input = [
            'name' => Helper::POST('name', ''),
            'url' => Helper::POST('url', ''),
            'type' => (int)Helper::POST('type', '1'),
            'parent_id' => (int)Helper::POST('parent_id', '0'),
            'weight' => (int)Helper::POST('weight', '0'),
        ];

        try {
            MenuBusiness::_()->update($id, $input);
            Helper::Show302($this->listUrl($show));
            return;
        } catch (\Exception $ex) {
            $data['error'] = $ex->getMessage();
        }
        $data['show'] = $show;
        $data['perm'] = $input + ['id' => $id];
        $data['title'] = '编辑菜单';
        $data['perm_tree'] = MenuBusiness::_()->getTree('all');
        $data['urls'] = [
            'update' => __url('System/menu_update'),
            'list' => $this->listUrl($show),
        ];
        $data['is_edit'] = true;
        Helper::Show($data, 'Menu/form');
    }

    /**
     * @menu_action 删除菜单
     */
    public function menu_delete()
    {
        $show = $this->showMode();
        $id = (int)Helper::GET('id', '0');
        try {
            MenuBusiness::_()->delete($id);
            Helper::Show302($this->listUrl($show));
        } catch (\Exception $ex) {
            Helper::Show302($this->listUrl($show, '&error=delete_failed'));
        }
    }

    /**
     * 把树形精简结构写入 config/scanned_menu.php（供对比用）
     */
    protected function saveScannedMenu(array $menuTree): bool
    {
        $file = __DIR__ . '/../config/scanned_menu.php';
        $export = var_export($menuTree, true);
        $content = "<?php\n// 扫描生成的菜单结构（树形，无 id/weight，url 为相对地址）\n"
            . "// 由 AdminTreeBuilder::build() 生成，供对比和手动调整\n"
            . "return {$export};\n";
        return file_put_contents($file, $content) !== false;
    }
}
