<?php declare(strict_types=1);
/**
 * DuckPhp Admin System Config
 */
return [
    // 应用名称
    'app_name' => 'Admin System',
    
    // 分页大小
    'page_size' => 15,
    
    // 菜单配置
    'menus' => [
        [
            'label' => '仪表盘',
            'url' => '',
            'icon' => 'bi bi-speedometer2',
        ],
        [
            'label' => '系统管理',
            'icon' => 'bi bi-gear',
            'children' => [
                ['label' => '用户管理', 'url' => 'user/index', 'icon' => 'bi bi-people'],
                ['label' => '角色管理', 'url' => 'role/index', 'icon' => 'bi bi-shield'],
                ['label' => '权限管理', 'url' => 'permission/index', 'icon' => 'bi bi-lock'],
            ],
        ],
    ],
];
