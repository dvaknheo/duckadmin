<?php

return [
    [
        'title' => '权限管理',
        'key' => 'auth',
        'icon' => 'layui-icon-vercode',
        'weight' => 900,
        'type' => 0,
        'children' => [
            [
                'title' => '账户管理',
                'key' => 'DuckAdmin\\Controller\\AdminController',
                'href' => 'admin/index',
                'type' => 1,
                'weight' => 1000,
            ],
            [
                'title' => '角色管理',
                'key' => 'DuckAdmin\\Controller\\RoleController',
                'href' => 'role/index',
                'type' => 1,
                'weight' => 900,
            ],
            [
                'title' => '菜单管理',
                'key' => 'DuckAdmin\\Controller\\RuleController',
                'href' => 'rule/index',
                'type' => 1,
                'weight' => 800,
            ],
        ]
    ],
    [
        'title' => '会员管理',
        'key' => 'user',
        'icon' => 'layui-icon-username',
        'weight' => 800,
        'type' => 0,
        'children' => [
            [
                'title' => '用户',
                'key' => 'DuckAdmin\\Controller\\UserController',
                'href' => 'User/index',
                'type' => 1,
                'weight' => 800,
            ]
        ]
    ],
    [
        'title' => '通用设置',
        'key' => 'common',
        'icon' => 'layui-icon-set',
        'weight' => 700,
        'type' => 0,
        'children' => [
            [
                'title' => '个人资料',
                'key' => 'DuckAdmin\\Controller\\AccountController',
                'href' => 'account/index',
                'type' => 1,
                'weight' => 800,
            ],
            [
                'title' => '系统设置',
                'key' => 'DuckAdmin\\Controller\\ConfigController',
                'href' => 'config/index',
                'type' => 1,
                'weight' => 500,
            ],
        ]
    ],    
];
