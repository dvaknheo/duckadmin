<?php
/**
 * 菜单配置（安装时使用）
 * type: 0=目录 1=菜单 2=操作
 */
return [
    [
        'name' => '系统管理',
        'url' => '',
        'type' => 0,
        'children' => [
            [
                'name' => '人员管理',
                'url' => 'Admin/index',
                'type' => 1,
                'children' => [
                    ['name' => '新增人员', 'url' => 'Admin/create', 'type' => 2],
                    ['name' => '编辑人员', 'url' => 'Admin/edit', 'type' => 2],
                    ['name' => '删除人员', 'url' => 'Admin/delete', 'type' => 2],
                ],
            ],
            [
                'name' => '职位管理',
                'url' => 'Role/index',
                'type' => 1,
                'children' => [
                    ['name' => '新增职位', 'url' => 'Role/create', 'type' => 2],
                    ['name' => '编辑职位', 'url' => 'Role/edit', 'type' => 2],
                    ['name' => '删除职位', 'url' => 'Role/delete', 'type' => 2],
                    ['name' => '权限分配', 'url' => 'Permission/index', 'type' => 1],
                ],
            ],
            [
                'name' => '菜单管理',
                'url' => 'Menu/index',
                'type' => 1,
                'children' => [
                    ['name' => '一键扫描', 'url' => 'Menu/scan', 'type' => 2],
                    ['name' => '新增菜单', 'url' => 'Menu/create', 'type' => 2],
                    ['name' => '编辑菜单', 'url' => 'Menu/edit', 'type' => 2],
                    ['name' => '删除菜单', 'url' => 'Menu/delete', 'type' => 2],
                ],
            ],
        ],
    ],
];
