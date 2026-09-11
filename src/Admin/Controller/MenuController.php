<?php declare(strict_types=1);
namespace DuckAdmin\Admin\Controller;

use DuckAdmin\Admin\Business\MenuBusiness;

/**
 * @menu_group 系统管理
 * @menu_directory 菜单管理 Menu/index
 * @menu_weight 40
 */
class MenuController extends Base
{
    /**
     * @menu_item 菜单管理
     * @menu_weight 10
     */
    public function index()
    {
        $data['tree'] = MenuBusiness::_()->getTree();
        $data['title'] = '权限和菜单管理';
        $data['current_route'] = 'system';

        $data['urls'] = [
            'list' => __url('Menu/index'),
            'create' => __url('Menu/create'),
            'edit' => __url('Menu/edit'),
            'delete' => __url('Menu/delete'),
            'scan' => __url('Menu/scan'),
        ];

        Helper::Show($data, 'Menu/index');
    }

    /**
     * @menu_action 一键扫描
     * @menu_weight 20
     */
    public function scan()
    {
        $added = MenuBusiness::_()->scanRoutes();
        $data['added'] = $added;
        $data['title'] = '一键扫描结果';
        $data['current_route'] = 'system';
        $data['urls'] = [
            'list' => __url('Menu/index'),
        ];
        Helper::Show($data, 'Menu/scan');
    }

    /**
     * @menu_action 新增菜单
     * @menu_weight 21
     */
    public function create()
    {
        $data['title'] = '新增菜单';
        $data['current_route'] = 'system';
        $data['permissions'] = MenuBusiness::_()->getAll();
        $data['urls'] = [
            'save' => __url('Menu/save'),
            'list' => __url('Menu/index'),
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
        $input = [
            'name' => Helper::POST('name', ''),
            'url' => Helper::POST('url', ''),
            'type' => (int)Helper::POST('type', '1'),
            'parent_id' => (int)Helper::POST('parent_id', '0'),
            'weight' => (int)Helper::POST('weight', '0'),
        ];

        $result = MenuBusiness::_()->create($input);
        if ($result['success']) {
            Helper::Show302(__url('Menu/index'));
            return;
        }
        $data['error'] = $result['message'];
        $data['title'] = '新增菜单';
        $data['current_route'] = 'system';
        $data['input'] = $input;
        $data['permissions'] = MenuBusiness::_()->getAll();
        $data['urls'] = [
            'save' => __url('Menu/save'),
            'list' => __url('Menu/index'),
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
        $id = (int)Helper::GET('id', '0');
        $perm = MenuBusiness::_()->getById($id);
        if (!$perm) {
            Helper::Show302(__url('Menu/index'));
            return;
        }

        $data['perm'] = $perm;
        $data['title'] = '编辑菜单';
        $data['current_route'] = 'system';
        $data['permissions'] = MenuBusiness::_()->getAll();
        $data['urls'] = [
            'update' => __url('Menu/update'),
            'list' => __url('Menu/index'),
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
            Helper::Show302(__url('Menu/index'));
            return;
        }
        $data['error'] = $result['message'];
        $data['perm'] = $input + ['id' => $id];
        $data['title'] = '编辑菜单';
        $data['current_route'] = 'system';
        $data['permissions'] = MenuBusiness::_()->getAll();
        $data['urls'] = [
            'update' => __url('Menu/update'),
            'list' => __url('Menu/index'),
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
        $id = (int)Helper::GET('id', '0');
        MenuBusiness::_()->delete($id);
        Helper::Show302(__url('Menu/index'));
    }
}
