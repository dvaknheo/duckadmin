<?php declare(strict_types=1);
namespace DuckAdmin\Admin\Controller;

use DuckAdmin\Admin\Business\MenuBusiness;
use DuckAdmin\Admin\Business\MenuConfigService;

/**
 * @menu_group 系统管理
 * @menu_directory 菜单管理 Menu/index
 * @menu_weight 40
 */
class MenuController extends Base
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
        return __url('Menu/index') . '?show=' . $show . $extra;
    }

    /**
     * @menu_item 菜单管理
     * @menu_weight 10
     */
    public function index()
    {
        $show = $this->showMode();

        $data['tree'] = MenuBusiness::_()->getTree($show);
        $data['show'] = $show;
        $data['title'] = '权限和菜单管理';

        $data['urls'] = [
            'list' => __url('Menu/index'),
            'create' => __url('Menu/create') . '?show=' . $show,
            'edit' => __url('Menu/edit'),
            'delete' => __url('Menu/delete'),
            'scan' => __url('Menu/scan') . '?show=' . $show,
        ];

        Helper::Show($data, 'Menu/index');
    }

    /**
     * @menu_action 一键扫描
     * @menu_weight 20
     */
    public function scan()
    {
        $show = $this->showMode();

        // 一步操作：扫描生成树形结构，补全 url，导入数据库
        $menuTree = MenuConfigService::_()->scanRoutes();
        $menuTree = (new \DuckAdmin\Admin\Business\AdminTreeBuilder())->resolveUrls($menuTree, __url(''));
        $added = MenuConfigService::_()->importToDb($menuTree);
        // 保存扫描结果到 config/scanned_menu.php 供对比
        MenuConfigService::_()->saveScannedMenu($menuTree);

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
     * @menu_weight 21
     */
    public function create()
    {
        $show = $this->showMode();

        $data['show'] = $show;
        $data['title'] = '新增菜单';
        $data['perm_tree'] = MenuBusiness::_()->getTree('all');
        $data['urls'] = [
            'save' => __url('Menu/save'),
            'list' => $this->listUrl($show),
        ];
        $data['is_edit'] = false;
        Helper::Show($data, 'Menu/form');
    }

    /**
     * @menu_action 保存菜单
     * @menu_weight 22
     */
    public function save()
    {
        $show = $this->showMode();
        $input = [
            'name' => Helper::POST('name', ''),
            'url' => Helper::POST('url', ''),
            'type' => (int)Helper::POST('type', '1'),
            'parent_id' => (int)Helper::POST('parent_id', '0'),
            'weight' => (int)Helper::POST('weight', '0'),
        ];

        $result = MenuBusiness::_()->create($input);
        if ($result['success']) {
            Helper::Show302($this->listUrl($show));
            return;
        }
        $data['error'] = $result['message'];
        $data['show'] = $show;
        $data['title'] = '新增菜单';
        $data['input'] = $input;
        $data['perm_tree'] = MenuBusiness::_()->getTree('all');
        $data['urls'] = [
            'save' => __url('Menu/save'),
            'list' => $this->listUrl($show),
        ];
        $data['is_edit'] = false;
        Helper::Show($data, 'Menu/form');
    }

    /**
     * @menu_action 编辑菜单
     * @menu_weight 23
     */
    public function edit()
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
            'update' => __url('Menu/update'),
            'list' => $this->listUrl($show),
        ];
        $data['is_edit'] = true;
        Helper::Show($data, 'Menu/form');
    }

    /**
     * @menu_action 更新菜单
     * @menu_weight 24
     */
    public function update()
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

        $result = MenuBusiness::_()->update($id, $input);
        if ($result['success']) {
            Helper::Show302($this->listUrl($show));
            return;
        }
        $data['error'] = $result['message'];
        $data['show'] = $show;
        $data['perm'] = $input + ['id' => $id];
        $data['title'] = '编辑菜单';
        $data['perm_tree'] = MenuBusiness::_()->getTree('all');
        $data['urls'] = [
            'update' => __url('Menu/update'),
            'list' => $this->listUrl($show),
        ];
        $data['is_edit'] = true;
        Helper::Show($data, 'Menu/form');
    }

    /**
     * @menu_action 删除菜单
     * @menu_weight 25
     */
    public function delete()
    {
        $show = $this->showMode();
        $id = (int)Helper::GET('id', '0');
        $ok = MenuBusiness::_()->delete($id);
        Helper::Show302($this->listUrl($show, $ok ? '' : '&error=has_children'));
    }
}
