<?php
// 扫描生成的菜单结构（树形，无 id/weight，url 为相对地址）
// 由 MenuConfigService::scanRoutes() 生成，供对比和手动调整
// 安装时由 MenuConfigService::installMenus() 读取并导入数据库
return array (
  0 => 
  array (
    'name' => '系统管理',
    'icon' => NULL,
    'url' => '',
    'type' => 0,
    'children' => 
    array (
      0 => 
      array (
        'name' => '人员管理',
        'icon' => NULL,
        'url' => 'Admin/#',
        'type' => 0,
        'children' => 
        array (
          0 => 
          array (
            'name' => '删除人员',
            'url' => 'Admin/delete',
            'type' => 2,
            'children' => 
            array (
            ),
          ),
          1 => 
          array (
            'name' => '更新人员',
            'url' => 'Admin/update',
            'type' => 2,
            'children' => 
            array (
            ),
          ),
          2 => 
          array (
            'name' => '编辑人员',
            'url' => 'Admin/edit',
            'type' => 2,
            'children' => 
            array (
            ),
          ),
          3 => 
          array (
            'name' => '保存人员',
            'url' => 'Admin/save',
            'type' => 2,
            'children' => 
            array (
            ),
          ),
          4 => 
          array (
            'name' => '新增人员',
            'url' => 'Admin/create',
            'type' => 2,
            'children' => 
            array (
            ),
          ),
          5 => 
          array (
            'name' => '人员管理',
            'url' => 'Admin/index',
            'type' => 1,
            'children' => 
            array (
            ),
          ),
        ),
      ),
      1 => 
      array (
        'name' => '菜单管理',
        'icon' => NULL,
        'url' => 'Menu/#',
        'type' => 0,
        'children' => 
        array (
          0 => 
          array (
            'name' => '删除菜单',
            'url' => 'Menu/delete',
            'type' => 2,
            'children' => 
            array (
            ),
          ),
          1 => 
          array (
            'name' => '更新菜单',
            'url' => 'Menu/update',
            'type' => 2,
            'children' => 
            array (
            ),
          ),
          2 => 
          array (
            'name' => '编辑菜单',
            'url' => 'Menu/edit',
            'type' => 2,
            'children' => 
            array (
            ),
          ),
          3 => 
          array (
            'name' => '保存菜单',
            'url' => 'Menu/save',
            'type' => 2,
            'children' => 
            array (
            ),
          ),
          4 => 
          array (
            'name' => '新增菜单',
            'url' => 'Menu/create',
            'type' => 2,
            'children' => 
            array (
            ),
          ),
          5 => 
          array (
            'name' => '一键扫描',
            'url' => 'Menu/scan',
            'type' => 2,
            'children' => 
            array (
            ),
          ),
          6 => 
          array (
            'name' => '菜单管理',
            'url' => 'Menu/index',
            'type' => 1,
            'children' => 
            array (
            ),
          ),
        ),
      ),
      2 => 
      array (
        'name' => '权限分配',
        'icon' => NULL,
        'url' => 'Permission/#',
        'type' => 0,
        'children' => 
        array (
          0 => 
          array (
            'name' => '权限分配',
            'url' => 'Permission/index',
            'type' => 1,
            'children' => 
            array (
            ),
          ),
        ),
      ),
      3 => 
      array (
        'name' => '职位管理',
        'icon' => NULL,
        'url' => 'Role/#',
        'type' => 0,
        'children' => 
        array (
          0 => 
          array (
            'name' => '删除职位',
            'url' => 'Role/delete',
            'type' => 2,
            'children' => 
            array (
            ),
          ),
          1 => 
          array (
            'name' => '更新职位',
            'url' => 'Role/update',
            'type' => 2,
            'children' => 
            array (
            ),
          ),
          2 => 
          array (
            'name' => '编辑职位',
            'url' => 'Role/edit',
            'type' => 2,
            'children' => 
            array (
            ),
          ),
          3 => 
          array (
            'name' => '保存职位',
            'url' => 'Role/save',
            'type' => 2,
            'children' => 
            array (
            ),
          ),
          4 => 
          array (
            'name' => '新增职位',
            'url' => 'Role/create',
            'type' => 2,
            'children' => 
            array (
            ),
          ),
          5 => 
          array (
            'name' => '职位管理',
            'url' => 'Role/index',
            'type' => 1,
            'children' => 
            array (
            ),
          ),
        ),
      ),
    ),
  ),
);
